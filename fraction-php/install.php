<?php
/**
 * Fraction Média — installateur (à SUPPRIMER du serveur après installation).
 */
require __DIR__ . '/app/bootstrap.php';

header('Content-Type: text/html; charset=utf-8');

if (installed()) {
    echo '<!doctype html><html lang="fr"><meta charset="utf-8"><body style="font-family:sans-serif;padding:3rem">';
    echo '<h1>Déjà installé</h1><p>Le CMS fonctionne déjà. Pour la sécurité, <strong>supprimez install.php et check.php du serveur</strong> (via FTP).</p>';
    echo '<p><a href="/admin/connexion">Aller à la page de connexion</a></p></body></html>';
    exit;
}

$erreur = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $username = trim((string)($_POST['username'] ?? ''));
    $nom = trim((string)($_POST['nom'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    if ($username === '' || strlen($password) < 8) {
        $erreur = 'Identifiant requis et mot de passe de 8 caractères minimum.';
    } else {
        install_schema();
        db()->prepare('INSERT INTO utilisateurs (username, password, nom, role, bio) VALUES (?, ?, ?, ?, ?)')
            ->execute([$username, password_hash($password, PASSWORD_DEFAULT), $nom !== '' ? $nom : $username, 'admin', '']);
        $adminId = (int)db()->lastInsertId();

        if (!empty($_POST['demo'])) {
            $rubriquesDemo = [
                ['Politique', 0], ['Sociologie', 1], ['Histoire', 2], ['Culture', 3],
            ];
            $idsRubriques = [];
            foreach ($rubriquesDemo as [$nomR, $ordre]) {
                db()->prepare('INSERT OR IGNORE INTO rubriques (nom, slug, description, ordre) VALUES (?, ?, ?, ?)')
                    ->execute([$nomR, slugify($nomR), 'Rubrique ' . $nomR, $ordre]);
                $idsRubriques[$nomR] = (int)db()->lastInsertId();
            }
            $tagsDemo = ['démocratie', 'travail', 'mémoire', 'patrimoine', 'image'];
            $idsTags = [];
            foreach ($tagsDemo as $nomT) {
                db()->prepare('INSERT OR IGNORE INTO tags (nom, slug) VALUES (?, ?)')->execute([$nomT, slugify($nomT)]);
                $st = db()->prepare('SELECT id FROM tags WHERE slug = ?');
                $st->execute([slugify($nomT)]);
                $idsTags[$nomT] = (int)$st->fetchColumn();
            }
            $articlesDemo = [
                ['La fabrique du désaccord démocratique', 'Comment les assemblées citoyennes réinventent la délibération — et ce qu\'elles disent de nos institutions.', 'Politique', true, 'uploads/demo-une.svg', 'Image de démonstration — Fraction Média'],
                ['Le travail invisible des soignantes', 'Soigner ne se réduit pas aux actes techniques. Enquête sur tout ce qui ne se compte pas.', 'Sociologie', false, '', ''],
                ['1968, la mémoire en rayons', 'Entre commémoration officielle et souvenirs militants, l\'année 1968 reste un champ de bataille historiographique.', 'Histoire', false, '', ''],
                ['Musées : à qui appartiennent les images ?', 'Open data et reproductions gratuites : les entrepôts d\'images bouleversent l\'économie du patrimoine.', 'Culture', false, '', ''],
            ];
            $corpsDemo = "## Introduction\n\nUn exemple de démonstration : remplacez ce texte depuis le back-office.\n\n> La délibération n'est pas la fin du conflit, c'est une autre manière de le tenir.\n\n- Première piste d'analyse\n- Deuxième piste d'analyse";
            $n = 0;
            foreach ($articlesDemo as [$titre, $chapo, $rub, $une, $image, $legende]) {
                $date = date('Y-m-d H:i:s', time() - $n * 86400 * 3);
                db()->prepare('INSERT OR IGNORE INTO articles (titre, slug, chapo, corps, image, legende, rubrique_id, statut, a_la_une, date_publication, cree_le) VALUES (?, ?, ?, ?, ?, ?, ?, "publie", ?, ?, ?)')
                    ->execute([$titre, slugify($titre), $chapo, $corpsDemo, $image, $legende, $idsRubriques[$rub], $une ? 1 : 0, $date, $date]);
                $st = db()->prepare('SELECT id FROM articles WHERE slug = ?');
                $st->execute([slugify($titre)]);
                $idArt = (int)$st->fetchColumn();
                db()->prepare('INSERT OR IGNORE INTO articles_auteurs (article_id, user_id) VALUES (?, ?)')->execute([$idArt, $adminId]);
                db()->prepare('INSERT OR IGNORE INTO articles_tags (article_id, tag_id) VALUES (?, ?)')->execute([$idArt, $idsTags[$tagsDemo[$n % count($tagsDemo)]]]);
                $n++;
            }
            db()->prepare('INSERT INTO galeries (titre, slug, description, photographe_id, publiee, date_publication) VALUES (?, ?, ?, ?, 1, ?)')
                ->execute(['Série de démonstration', slugify('Série de démonstration'), 'Galerie d\'exemple créée par l\'installateur.', $adminId, now_sql()]);
            $idGal = (int)db()->lastInsertId();
            foreach (['uploads/demo-photo-1.svg', 'uploads/demo-photo-2.svg', 'uploads/demo-photo-3.svg'] as $i => $img) {
                db()->prepare('INSERT INTO photos (galerie_id, image, legende, credit, ordre, mise_en_avant) VALUES (?, ?, ?, ?, ?, ?)')
                    ->execute([$idGal, $img, 'Légende de démonstration ' . ($i + 1), '© Fraction Média', $i * 10, $i === 0 ? 1 : 0]);
            }
            db()->prepare('INSERT INTO oeuvres (titre, artiste, annee, technique, description, image, publiee, date_publication) VALUES (?, ?, ?, ?, ?, ?, 1, ?)')
                ->execute(['Sans titre (fragment I)', 'Inès Ferreira', 2024, 'photographie argentique', 'Premier volet d\'une série sur les fractures urbaines.', 'uploads/demo-oeuvre.svg', now_sql()]);
        }

        echo '<!doctype html><html lang="fr"><meta charset="utf-8"><body style="font-family:sans-serif;padding:3rem;max-width:640px">';
        echo '<h1>✓ Installation réussie</h1>';
        echo '<p><strong>Étape de sécurité immédiate :</strong> connectez-vous via FTP et <strong>supprimez <code>install.php</code> et <code>check.php</code></strong> du serveur.</p>';
        echo '<p>Votre identifiant admin : <strong>' . htmlspecialchars($username) . '</strong></p>';
        echo '<p><a href="/admin/connexion" style="font-size:1.1rem">Se connecter au back-office →</a> — <a href="/">voir le site</a></p>';
        echo '</body></html>';
        exit;
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Installation — <?php echo htmlspecialchars(SITE_NOM); ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="admin-body">
<div class="container connexion-card">
    <h1 class="section-titre">Installer Fraction Média</h1>
    <p class="aide">Ceci crée les tables de la base et votre compte administrateur. À faire <strong>une seule fois</strong>.</p>
    <?php if ($erreur): ?><p class="flash flash-error"><?php echo htmlspecialchars($erreur); ?></p><?php endif; ?>
    <form method="post">
        <?php echo csrf_field(); ?>
        <div class="champ"><label for="iusername">Identifiant admin *</label><input type="text" id="iusername" name="username" required></div>
        <div class="champ"><label for="inom">Nom affiché (byline)</label><input type="text" id="inom" name="nom"></div>
        <div class="champ"><label for="ipassword">Mot de passe * (8 caractères min.)</label><input type="password" id="ipassword" name="password" required minlength="8"></div>
        <div class="champ"><label style="font-weight:400"><input type="checkbox" name="demo" value="1" checked> Installer un contenu de démonstration (rubriques, articles, galerie, œuvre)</label></div>
        <button class="bouton" type="submit">Installer</button>
    </form>
</div>
</body>
</html>
