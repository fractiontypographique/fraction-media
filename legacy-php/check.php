<?php
/**
 * Fraction Média — vérification d'environnement (à supprimer après installation).
 */
require __DIR__ . '/app/config.php';

header('Content-Type: text/html; charset=utf-8');
echo '<!doctype html><html lang="fr"><head><meta charset="utf-8"><title>Vérification — ' . htmlspecialchars(SITE_NOM) . '</title>';
echo '<link rel="stylesheet" href="/assets/css/style.css"></head><body class="admin-body"><div class="container" style="max-width:680px;padding-top:2rem"><h1 class="section-titre">Vérification de l\'hébergement</h1>';

$ok = true;
function ligne(bool $etat, string $label): void {
    global $ok;
    echo '<p class="flash ' . ($etat ? 'flash-succes' : 'flash-error') . '">' . ($etat ? '✓ ' : '✗ ') . htmlspecialchars($label) . '</p>';
    if (!$etat) { $ok = false; }
}

ligne(version_compare(PHP_VERSION, '7.4', '>='), 'PHP ≥ 7.4 (version : ' . PHP_VERSION . ')');
ligne(extension_loaded('pdo'), 'Extension PDO');
ligne(extension_loaded('pdo_sqlite') || extension_loaded('pdo_mysql'), 'Driver de base (pdo_sqlite' . (extension_loaded('pdo_mysql') ? ' + pdo_mysql' : '') . (extension_loaded('pdo_sqlite') ? ' + pdo_sqlite' : '') . ')');
ligne(function_exists('session_start'), 'Sessions');
ligne(function_exists('iconv'), 'iconv (slugs)');
ligne(function_exists('random_bytes'), 'random_bytes (CSRF/mots de passe)');
ligne(is_dir(__DIR__ . '/data'), 'Dossier data/ présent');
ligne(is_writable(__DIR__ . '/data') || DB_DRIVER === 'mysql', 'data/ inscriptible (droits 775 via FTP si besoin)');
ligne(is_dir(__DIR__ . '/uploads'), 'Dossier uploads/ présent');
ligne(is_writable(__DIR__ . '/uploads'), 'uploads/ inscriptible (droits 775 via FTP si besoin)');

$ht = file_exists(__DIR__ . '/.htaccess');
ligne($ht, '.htaccess présent (réécriture d\'URL — Apache)');
echo '<p style="color:#555;font-size:0.9rem">ℹ Si les liens « propres » (/article/…) ne fonctionnent pas après installation, le site reste accessible via /index.php?route=article… — mod_rewrite dépend de l\'hébergeur.</p>';

if ($ok) {
    echo '<p><a class="bouton" href="/install.php">Suite : installer Fraction Média →</a></p>';
} else {
    echo '<p>Corrigez les points en rouge (généralement les droits de dossiers via FTP), puis rechargez cette page.</p>';
}
echo '</div></body></html>';
