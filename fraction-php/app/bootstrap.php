<?php
/**
 * Fraction Média — noyau de l'application (PHP >= 7.4).
 */
if (!defined('APP_DEBUG')) {
    define('APP_DEBUG', false);
}
error_reporting(E_ALL);
ini_set('display_errors', APP_DEBUG ? '1' : '0');

require_once __DIR__ . '/config.php';

define('APP_ROOT', dirname(__DIR__));
define('DATA_DIR', APP_ROOT . '/data');
define('UPLOAD_DIR', APP_ROOT . '/uploads');
define('VIEWS_DIR', __DIR__ . '/views');

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

function root_url(string $path = ''): string {
    return '/' . ltrim($path, '/');
}

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        if (DB_DRIVER === 'mysql') {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } else {
            if (!is_dir(DATA_DIR)) {
                mkdir(DATA_DIR, 0775, true);
            }
            $pdo = new PDO('sqlite:' . DATA_DIR . '/site.sqlite');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $pdo->exec('PRAGMA foreign_keys = ON');
            $pdo->exec('PRAGMA journal_mode = WAL');
            $pdo->exec('PRAGMA busy_timeout = 5000');
        }
    }
    return $pdo;
}

function e($s): string {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function slugify(string $text): string {
    $t = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
    if ($t === false || $t === '') {
        $t = $text;
    }
    $t = strtolower($t);
    $t = preg_replace("/['’]/", '', $t);
    $t = preg_replace('/[^a-z0-9]+/', '-', $t);
    $t = trim($t, '-');
    return $t === '' ? 'sans-titre' : $t;
}

function unique_slug(string $table, string $base, int $ignoreId = 0): string {
    $slug = slugify($base);
    $i = 1;
    while (true) {
        $st = db()->prepare("SELECT COUNT(*) FROM `$table` WHERE slug = ?" . ($ignoreId ? ' AND id != ?' : ''));
        $st->execute($ignoreId ? [$slug, $ignoreId] : [$slug]);
        if ((int)$st->fetchColumn() === 0) {
            return $slug;
        }
        $i++;
        $slug = slugify($base) . '-' . $i;
    }
}

function csrf_token(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string {
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function require_csrf(): void {
    if (!isset($_POST['csrf'], $_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], (string)$_POST['csrf'])) {
        http_response_code(403);
        exit('Requête invalide (jeton CSRF manquant ou expiré). Rechargez la page et réessayez.');
    }
}

function current_user(): ?array {
    return isset($_SESSION['user']) && is_array($_SESSION['user']) ? $_SESSION['user'] : null;
}

function is_admin(): bool {
    $u = current_user();
    return $u !== null && $u['role'] === 'admin';
}

function require_login(): void {
    if (current_user() === null) {
        header('Location: ' . root_url('admin/connexion'));
        exit;
    }
}

function flash(string $type, string $message): void {
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function take_flashes(): array {
    $f = isset($_SESSION['flash']) ? $_SESSION['flash'] : [];
    unset($_SESSION['flash']);
    return $f;
}

function insert_ignore(string $sql): string {
    return DB_DRIVER === 'mysql' ? str_replace('INSERT OR IGNORE', 'INSERT IGNORE', $sql) : $sql;
}

function now_sql(): string {
    return date('Y-m-d H:i:s');
}

function temps_lecture(string $corps): int {
    return max(1, (int)round(str_word_count_utf8($corps) / 200));
}

function str_word_count_utf8(string $s): int {
    $m = preg_match_all('/[\pL\pN][\pL\pN\-\'’]*/u', $s);
    return $m === false ? 0 : $m;
}

function date_fr(string $iso): string {
    $mois = [1 => 'janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
    $ts = strtotime($iso);
    if ($ts === false) {
        return $iso;
    }
    $j = (int)date('j', $ts);
    $m = (int)date('n', $ts);
    return $j . ' ' . $mois[$m] . ' ' . date('Y', $ts);
}

/* ------------------------------------------------------------------ */
/* Markdown léger : titres, citations, listes, gras, italique, code, liens */

function md_inline(string $s): string {
    $s = e($s);
    $s = preg_replace('/\[([^\]]+)\]\(((?:https?:\/\/|\/)[^\s]+)\)/', '<a href="$2">$1</a>', $s);
    $s = preg_replace('/\*\*([^*]+)\*\*/', '<strong>$1</strong>', $s);
    $s = preg_replace('/\*([^*]+)\*/', '<em>$1</em>', $s);
    $s = preg_replace('/`([^`]+)`/', '<code>$1</code>', $s);
    $s = nl2br($s);
    return $s;
}

function markdown(string $text): string {
    $text = str_replace("\r\n", "\n", trim((string)$text));
    $blocks = preg_split('/\n{2,}/', $text);
    $out = [];
    foreach ($blocks as $block) {
        $b = trim($block);
        if ($b === '') {
            continue;
        }
        if (preg_match('/^#{1,3}\s+(.+)$/s', $b, $m)) {
            $niveau = ($b[0] === '#') ? substr($b, 0, strspn($b, '#')) : '#';
            $tag = strlen($niveau) >= 2 ? 'h2' : 'h3'; // pas de h1 : réservé au titre de l'article
            $out[] = '<' . $tag . '>' . md_inline(trim(ltrim($b, '# '))) . '</' . $tag . '>';
        } elseif ($b[0] === '>') {
            $lignes = [];
            foreach (explode("\n", $b) as $l) {
                $lignes[] = preg_replace('/^>\s?/', '', $l);
            }
            $out[] = '<blockquote><p>' . md_inline(implode(' ', $lignes)) . '</p></blockquote>';
        } elseif (preg_match('/^[-*]\s+/', $b)) {
            $items = [];
            foreach (explode("\n", $b) as $l) {
                if (preg_match('/^[-*]\s+(.+)$/', trim($l), $m)) {
                    $items[] = '<li>' . md_inline($m[1]) . '</li>';
                }
            }
            if ($items) {
                $out[] = '<ul>' . implode('', $items) . '</ul>';
            }
        } else {
            $out[] = '<p>' . md_inline($b) . '</p>';
        }
    }
    return implode("\n", $out);
}

/* ------------------------------------------------------------------ */
/* Envois d'images */

function upload_image(array $file, string $prefix = 'img'): ?string {
    if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
        flash('error', 'Envoi impossible (fichier manquant ou refusé par le serveur).');
        return null;
    }
    if ($file['size'] > 20 * 1024 * 1024) {
        flash('error', 'Image trop volumineuse (maximum 20 Mo).');
        return null;
    }
    $info = @getimagesize($file['tmp_name']);
    if ($info === false) {
        flash('error', 'Le fichier n’est pas une image valide.');
        return null;
    }
    $exts = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_GIF => 'gif', IMAGETYPE_WEBP => 'webp'];
    if (!isset($exts[$info[2]])) {
        flash('error', 'Format accepté : JPG, PNG, GIF ou WEBP.');
        return null;
    }
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0775, true);
    }
    if (!is_writable(UPLOAD_DIR)) {
        flash('error', 'Le dossier uploads/ n’est pas inscriptible. Réglez ses droits via votre client FTP (775).');
        return null;
    }
    $base = slugify(pathinfo($file['name'], PATHINFO_FILENAME));
    $name = $prefix . '-' . $base . '-' . bin2hex(random_bytes(4)) . '.' . $exts[$info[2]];
    if (!move_uploaded_file($file['tmp_name'], UPLOAD_DIR . '/' . $name)) {
        flash('error', 'Échec de l’enregistrement du fichier.');
        return null;
    }
    return 'uploads/' . $name;
}

/* ------------------------------------------------------------------ */
/* Schéma de la base */

function schema_tables(): array {
    $sqliteAuto = 'INTEGER PRIMARY KEY AUTOINCREMENT';
    $mysqlAuto = 'INT AUTO_INCREMENT PRIMARY KEY';
    $pk = function (string $col) {
        return DB_DRIVER === 'mysql' ? "{$col} INT AUTO_INCREMENT PRIMARY KEY" : "{$col} INTEGER PRIMARY KEY AUTOINCREMENT";
    };
    $fkInt = DB_DRIVER === 'mysql' ? 'INT' : 'INTEGER';
    return [
        "CREATE TABLE IF NOT EXISTS rubriques (
            {$pk('id')}, nom VARCHAR(190) NOT NULL UNIQUE, slug VARCHAR(190) NOT NULL UNIQUE,
            description TEXT, ordre INT DEFAULT 0
        )",
        "CREATE TABLE IF NOT EXISTS utilisateurs (
            {$pk('id')}, username VARCHAR(60) NOT NULL UNIQUE, password VARCHAR(255) NOT NULL,
            nom VARCHAR(120) DEFAULT '', role VARCHAR(20) DEFAULT 'redacteur', bio TEXT, avatar VARCHAR(255) DEFAULT ''
        )",
        "CREATE TABLE IF NOT EXISTS tags (
            {$pk('id')}, nom VARCHAR(80) NOT NULL UNIQUE, slug VARCHAR(100) NOT NULL UNIQUE
        )",
        "CREATE TABLE IF NOT EXISTS articles (
            {$pk('id')}, titre VARCHAR(250) NOT NULL, slug VARCHAR(280) NOT NULL UNIQUE,
            chapo TEXT, corps TEXT, image VARCHAR(255) DEFAULT '', legende VARCHAR(250) DEFAULT '',
            rubrique_id {$fkInt}, statut VARCHAR(12) DEFAULT 'brouillon',
            a_la_une INTEGER DEFAULT 0, date_publication VARCHAR(19) DEFAULT '',
            cree_le VARCHAR(19) DEFAULT ''
        )",
        "CREATE TABLE IF NOT EXISTS articles_auteurs (
            article_id {$fkInt} NOT NULL, user_id {$fkInt} NOT NULL, PRIMARY KEY (article_id, user_id)
        )",
        "CREATE TABLE IF NOT EXISTS articles_tags (
            article_id {$fkInt} NOT NULL, tag_id {$fkInt} NOT NULL, PRIMARY KEY (article_id, tag_id)
        )",
        "CREATE TABLE IF NOT EXISTS galeries (
            {$pk('id')}, titre VARCHAR(200) NOT NULL, slug VARCHAR(220) NOT NULL UNIQUE,
            description TEXT, photographe_id {$fkInt} NULL, date_publication VARCHAR(19) DEFAULT '',
            publiee INTEGER DEFAULT 1
        )",
        "CREATE TABLE IF NOT EXISTS photos (
            {$pk('id')}, galerie_id {$fkInt} NOT NULL, image VARCHAR(255) NOT NULL,
            legende VARCHAR(250) DEFAULT '', credit VARCHAR(120) DEFAULT '', ordre INT DEFAULT 0,
            mise_en_avant INTEGER DEFAULT 0
        )",
        "CREATE TABLE IF NOT EXISTS oeuvres (
            {$pk('id')}, titre VARCHAR(200) NOT NULL, artiste VARCHAR(120) DEFAULT '',
            annee INT NULL, technique VARCHAR(160) DEFAULT '', description TEXT,
            image VARCHAR(255) NOT NULL, rubrique_id {$fkInt} NULL,
            date_publication VARCHAR(19) DEFAULT '', publiee INTEGER DEFAULT 1
        )",
    ];
}

function install_schema(): void {
    foreach (schema_tables() as $sql) {
        db()->exec($sql);
    }
}

function installed(): bool {
    if (DB_DRIVER === 'sqlite' && !file_exists(DATA_DIR . '/site.sqlite')) {
        return false;
    }
    try {
        return (int)db()->query('SELECT COUNT(*) FROM utilisateurs')->fetchColumn() > 0;
    } catch (Throwable $t) {
        return false;
    }
}

/* ------------------------------------------------------------------ */
/* Rendu des vues */

function render(string $view, array $vars = [], string $layout = 'layout'): void {
    extract($vars, EXTR_SKIP);
    ob_start();
    require VIEWS_DIR . '/' . $view . '.php';
    $content = ob_get_clean();
    require VIEWS_DIR . '/' . $layout . '.php';
    exit;
}

function page404(string $message = 'Page introuvable.'): void {
    http_response_code(404);
    render('404', ['titre' => '404', 'message' => $message]);
}

require_once __DIR__ . '/models.php';
