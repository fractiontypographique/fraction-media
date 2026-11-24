<?php
/**
 * Fraction Média — back-office (/admin/...).
 */
require dirname(__DIR__) . '/bootstrap.php';

$page = isset($segments[1]) && $segments[1] !== '' ? $segments[1] : 'tableau-de-bord';
$action = isset($segments[2]) ? $segments[2] : '';
$id = isset($segments[3]) ? (int)$segments[3] : 0;
$method = $_SERVER['REQUEST_METHOD'];

/* ---------------- Connexion / déconnexion ---------------- */

if ($page === 'connexion') {
    if (current_user() !== null) {
        header('Location: ' . root_url('admin/'));
        exit;
    }
    if ($method === 'POST') {
        require_csrf();
        $u = utilisateur_par_identifiant(trim((string)($_POST['username'] ?? '')));
        if ($u !== null && password_verify((string)($_POST['password'] ?? ''), $u['password'])) {
            session_regenerate_id(true);
            $_SESSION['user'] = ['id' => (int)$u['id'], 'username' => $u['username'], 'nom' => $u['nom'], 'role' => $u['role']];
            header('Location: ' . root_url('admin/'));
            exit;
        }
        flash('error', 'Identifiant ou mot de passe incorrect.');
    }
    render('admin/login', ['titre' => 'Connexion — ' . SITE_SIGLE], 'layout');
}

if ($page === 'deconnexion') {
    $_SESSION = [];
    session_destroy();
    header('Location: ' . root_url('/'));
    exit;
}

require_login();

/* ---------------- Tableau de bord ---------------- */

if ($page === 'tableau-de-bord') {
    render('admin/dashboard', [
        'titre' => 'Back-office — ' . SITE_SIGLE,
        'nb_articles' => (int)db()->query('SELECT COUNT(*) FROM articles')->fetchColumn(),
        'nb_publies' => (int)db()->query('SELECT COUNT(*) FROM articles WHERE statut = "publie"')->fetchColumn(),
        'nb_galeries' => (int)db()->query('SELECT COUNT(*) FROM galeries')->fetchColumn(),
        'nb_oeuvres' => (int)db()->query('SELECT COUNT(*) FROM oeuvres')->fetchColumn(),
        'recents' => db()->query('SELECT a.*, r.nom AS rubrique_nom FROM articles a JOIN rubriques r ON r.id = a.rubrique_id ORDER BY a.cree_le DESC LIMIT 8')->fetchAll(),
    ], 'admin/layout_admin');
}

/* ---------------- Articles ---------------- */

if ($page === 'articles') {
    if ($method === 'POST' && $action === 'supprimer') {
        require_csrf();
        db()->prepare('DELETE FROM articles WHERE id = ?')->execute([$id]);
        db()->prepare('DELETE FROM articles_auteurs WHERE article_id = ?')->execute([$id]);
        db()->prepare('DELETE FROM articles_tags WHERE article_id = ?')->execute([$id]);
        flash('succes', 'Article supprimé.');
        header('Location: ' . root_url('admin/articles'));
        exit;
    }

    if ($method === 'POST' && ($action === 'nouveau' || $action === 'enregistrer')) {
        require_csrf();
        $titre = trim((string)($_POST['titre'] ?? ''));
        $rubrique = (int)($_POST['rubrique_id'] ?? 0);
        if ($titre === '' || $rubrique === 0) {
            flash('error', 'Le titre et la rubrique sont obligatoires.');
            header('Location: ' . root_url('admin/articles/' . ($action === 'nouveau' ? 'nouveau' : 'editer/' . $id)));
            exit;
        }
        $champs = [
            'titre' => $titre,
            'chapo' => trim((string)($_POST['chapo'] ?? '')),
            'corps' => (string)($_POST['corps'] ?? ''),
            'rubrique_id' => $rubrique,
            'statut' => in_array($_POST['statut'] ?? '', ['brouillon', 'public', 'publie'], true) ? $_POST['statut'] : 'brouillon',
            'a_la_une' => !empty($_POST['a_la_une']) ? 1 : 0,
            'legende' => trim((string)($_POST['legende'] ?? '')),
        ];
        $date = trim((string)($_POST['date_publication'] ?? ''));
        $champs['date_publication'] = $date !== '' ? str_replace('T', ' ', $date) . ':00' : now_sql();

        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $chemin = upload_image($_FILES['image'], 'article');
            if ($chemin !== null) {
                $champs['image'] = $chemin;
            }
        }

        if ($action === 'nouveau') {
            $champs['slug'] = unique_slug('articles', isset($_POST['slug']) && trim((string)$_POST['slug']) !== '' ? (string)$_POST['slug'] : $titre);
            $champs['cree_le'] = now_sql();
            $cols = implode(', ', array_keys($champs));
            $marks = implode(', ', array_fill(0, count($champs), '?'));
            $sql = "INSERT INTO articles ($cols) VALUES ($marks)";
            db()->prepare($sql)->execute(array_values($champs));
            $idArt = (int)db()->lastInsertId();
        } else {
            $existant = db()->prepare('SELECT * FROM articles WHERE id = ?');
            $existant->execute([$id]);
            $avant = $existant->fetch();
            if (!$avant) {
                page404('Article inconnu.');
            }
            $champs['slug'] = unique_slug('articles', isset($_POST['slug']) && trim((string)$_POST['slug']) !== '' ? (string)$_POST['slug'] : $titre, $id);
            $sets = implode(' = ?, ', array_keys($champs)) . ' = ?';
            $val = array_values($champs);
            $val[] = $id;
            db()->prepare("UPDATE articles SET $sets WHERE id = ?")->execute($val);
            $idArt = $id;
        }

        db()->prepare('DELETE FROM articles_auteurs WHERE article_id = ?')->execute([$idArt]);
        db()->prepare('DELETE FROM articles_tags WHERE article_id = ?')->execute([$idArt]);
        foreach ((array)($_POST['auteurs'] ?? []) as $uid) {
            db()->prepare(insert_ignore('INSERT OR IGNORE INTO articles_auteurs (article_id, user_id) VALUES (?, ?)'))->execute([$idArt, (int)$uid]);
        }
        foreach ((array)($_POST['tags'] ?? []) as $tid) {
            db()->prepare(insert_ignore('INSERT OR IGNORE INTO articles_tags (article_id, tag_id) VALUES (?, ?)'))->execute([$idArt, (int)$tid]);
        }
        flash('succes', 'Article enregistré.');
        header('Location: ' . root_url('admin/articles'));
        exit;
    }

    if ($action === 'nouveau' || ($action === 'editer' && $id > 0)) {
        $article = null;
        if ($action === 'editer') {
            $st = db()->prepare('SELECT * FROM articles WHERE id = ?');
            $st->execute([$id]);
            $article = $st->fetch() ?: null;
            if ($article === null) {
                page404('Article inconnu.');
            }
        }
        $auteursChoisis = $tagsChoisis = [];
        if ($article) {
            $st = db()->prepare('SELECT user_id FROM articles_auteurs WHERE article_id = ?');
            $st->execute([$article['id']]);
            $auteursChoisis = array_map('intval', $st->fetchAll(PDO::FETCH_COLUMN));
            $st = db()->prepare('SELECT tag_id FROM articles_tags WHERE article_id = ?');
            $st->execute([$article['id']]);
            $tagsChoisis = array_map('intval', $st->fetchAll(PDO::FETCH_COLUMN));
        }
        render('admin/article_form', [
            'titre' => ($article ? 'Modifier : ' . $article['titre'] : 'Nouvel article') . ' — ' . SITE_SIGLE,
            'article' => $article,
            'rubriques' => db()->query('SELECT * FROM rubriques ORDER BY ordre, nom')->fetchAll(),
            'tousAuteurs' => db()->query('SELECT id, nom, username FROM utilisateurs ORDER BY nom')->fetchAll(),
            'tousTags' => db()->query('SELECT * FROM tags ORDER BY nom')->fetchAll(),
            'auteursChoisis' => $auteursChoisis,
            'tagsChoisis' => $tagsChoisis,
        ], 'admin/layout_admin');
    }

    $filtre = (string)($_GET['statut'] ?? '');
    if (in_array($filtre, ['brouillon', 'public', 'publie'], true)) {
        $st = db()->prepare('SELECT a.*, r.nom AS rubrique_nom FROM articles a JOIN rubriques r ON r.id = a.rubrique_id WHERE a.statut = ? ORDER BY a.date_publication DESC');
        $st->execute([$filtre]);
    } else {
        $st = db()->query('SELECT a.*, r.nom AS rubrique_nom FROM articles a JOIN rubriques r ON r.id = a.rubrique_id ORDER BY a.date_publication DESC');
    }
    render('admin/articles', ['titre' => 'Articles — ' . SITE_SIGLE, 'articles' => $st->fetchAll(), 'filtre' => $filtre], 'admin/layout_admin');
}

/* ---------------- Rubriques ---------------- */

if ($page === 'rubriques') {
    if ($method === 'POST' && $action === 'enregistrer') {
        require_csrf();
        $nom = trim((string)($_POST['nom'] ?? ''));
        if ($nom !== '') {
            if ($id > 0) {
                $slug = unique_slug('rubriques', trim((string)($_POST['slug'] ?? '')) !== '' ? (string)$_POST['slug'] : $nom, $id);
                db()->prepare('UPDATE rubriques SET nom = ?, slug = ?, description = ?, ordre = ? WHERE id = ?')
                    ->execute([$nom, $slug, trim((string)($_POST['description'] ?? '')), (int)($_POST['ordre'] ?? 0), $id]);
            } else {
                $slug = unique_slug('rubriques', trim((string)($_POST['slug'] ?? '')) !== '' ? (string)$_POST['slug'] : $nom);
                db()->prepare('INSERT INTO rubriques (nom, slug, description, ordre) VALUES (?, ?, ?, ?)')
                    ->execute([$nom, $slug, trim((string)($_POST['description'] ?? '')), (int)($_POST['ordre'] ?? 0)]);
            }
            flash('succes', 'Rubrique enregistrée.');
        }
        header('Location: ' . root_url('admin/rubriques'));
        exit;
    }
    if ($method === 'POST' && $action === 'supprimer') {
        require_csrf();
        $nb = db()->prepare('SELECT COUNT(*) FROM articles WHERE rubrique_id = ?');
        $nb->execute([$id]);
        if ((int)$nb->fetchColumn() > 0) {
            flash('error', 'Impossible : des articles existent dans cette rubrique. Déplacez-les d’abord.');
        } else {
            db()->prepare('DELETE FROM rubriques WHERE id = ?')->execute([$id]);
            flash('succes', 'Rubrique supprimée.');
        }
        header('Location: ' . root_url('admin/rubriques'));
        exit;
    }
    if ($action === 'editer' && $id > 0 && $method === 'GET') {
        $st = db()->prepare('SELECT * FROM rubriques WHERE id = ?');
        $st->execute([$id]);
        $editer = $st->fetch() ?: null;
        render('admin/rubriques', ['titre' => 'Rubriques — ' . SITE_SIGLE,
            'rubriques' => db()->query('SELECT * FROM rubriques ORDER BY ordre, nom')->fetchAll(),
            'editer' => $editer], 'admin/layout_admin');
    }
    render('admin/rubriques', ['titre' => 'Rubriques — ' . SITE_SIGLE,
        'rubriques' => db()->query('SELECT * FROM rubriques ORDER BY ordre, nom')->fetchAll(),
        'editer' => null], 'admin/layout_admin');
}

/* ---------------- Galeries photographiques ---------------- */

if ($page === 'galeries') {
    if ($method === 'POST' && $action === 'enregistrer') {
        require_csrf();
        $titre = trim((string)($_POST['titre'] ?? ''));
        if ($titre !== '') {
            $photographe = (int)($_POST['photographe_id'] ?? 0);
            if ($id > 0) {
                db()->prepare('UPDATE galeries SET titre = ?, slug = ?, description = ?, photographe_id = ?, publiee = ? WHERE id = ?')
                    ->execute([$titre, unique_slug('galeries', $titre, $id), trim((string)($_POST['description'] ?? '')),
                        $photographe ?: null, !empty($_POST['publiee']) ? 1 : 0, $id]);
                $idGal = $id;
            } else {
                db()->prepare('INSERT INTO galeries (titre, slug, description, photographe_id, publiee, date_publication) VALUES (?, ?, ?, ?, ?, ?)')
                    ->execute([$titre, unique_slug('galeries', $titre), trim((string)($_POST['description'] ?? '')),
                        $photographe ?: null, !empty($_POST['publiee']) ? 1 : 0, now_sql()]);
                $idGal = (int)db()->lastInsertId();
            }
            flash('succes', 'Galerie enregistrée.');
            header('Location: ' . root_url('admin/galeries/editer/' . $idGal));
            exit;
        }
        header('Location: ' . root_url('admin/galeries'));
        exit;
    }
    if ($method === 'POST' && $action === 'supprimer') {
        require_csrf();
        $ps = db()->prepare('SELECT id FROM photos WHERE galerie_id = ?');
        $ps->execute([$id]);
        db()->prepare('DELETE FROM photos WHERE galerie_id = ?')->execute([$id]);
        db()->prepare('DELETE FROM galeries WHERE id = ?')->execute([$id]);
        flash('succes', 'Galerie supprimée.');
        header('Location: ' . root_url('admin/galeries'));
        exit;
    }
    if ($method === 'POST' && $action === 'photo-ajouter') {
        require_csrf();
        $chemin = isset($_FILES['image']) ? upload_image($_FILES['image'], 'photo') : null;
        if ($chemin !== null) {
            db()->prepare('INSERT INTO photos (galerie_id, image, legende, credit, ordre, mise_en_avant) VALUES (?, ?, ?, ?, ?, ?)')
                ->execute([$id, $chemin, trim((string)($_POST['legende'] ?? '')), trim((string)($_POST['credit'] ?? '')),
                    (int)($_POST['ordre'] ?? 0), !empty($_POST['mise_en_avant']) ? 1 : 0]);
            flash('succes', 'Photographie ajoutée.');
        }
        header('Location: ' . root_url('admin/galeries/editer/' . $id));
        exit;
    }
    if ($method === 'POST' && $action === 'photo-enregistrer') {
        require_csrf();
        db()->prepare('UPDATE photos SET legende = ?, credit = ?, ordre = ?, mise_en_avant = ? WHERE id = ? AND galerie_id = ?')
            ->execute([trim((string)($_POST['legende'] ?? '')), trim((string)($_POST['credit'] ?? '')),
                (int)($_POST['ordre'] ?? 0), !empty($_POST['mise_en_avant']) ? 1 : 0, $id, (int)($_POST['galerie_id'] ?? 0)]);
        flash('succes', 'Photographie mise à jour.');
        header('Location: ' . root_url('admin/galeries/editer/' . ((int)($_POST['galerie_id'] ?? 0))));
        exit;
    }
    if ($method === 'POST' && $action === 'photo-supprimer') {
        require_csrf();
        db()->prepare('DELETE FROM photos WHERE id = ?')->execute([$id]);
        flash('succes', 'Photographie supprimée.');
        header('Location: ' . root_url('admin/galeries/editer/' . ((int)($_POST['galerie_id'] ?? 0))));
        exit;
    }
    if ($action === 'editer' && $method === 'GET') {
        $editer = null;
        if ($id > 0) {
            $st = db()->prepare('SELECT * FROM galeries WHERE id = ?');
            $st->execute([$id]);
            $editer = $st->fetch() ?: null;
            if ($editer === null) {
                page404('Galerie inconnue.');
            }
        }
        render('admin/galerie_form', ['titre' => ($editer ? 'Galerie : ' . $editer['titre'] : 'Nouvelle galerie') . ' — ' . SITE_SIGLE,
            'editer' => $editer, 'photos' => photos_de_galerie($id),
            'photographes' => db()->query('SELECT id, nom, username FROM utilisateurs ORDER BY nom')->fetchAll()],
            'admin/layout_admin');
    }
    $galeries = db()->query('SELECT g.*, (SELECT COUNT(*) FROM photos p WHERE p.galerie_id = g.id) AS nb_photos FROM galeries g ORDER BY g.date_publication DESC')->fetchAll();
    render('admin/galeries', ['titre' => 'Galeries — ' . SITE_SIGLE, 'galeries' => $galeries], 'admin/layout_admin');
}

/* ---------------- Œuvres d'art ---------------- */

if ($page === 'oeuvres') {
    if ($method === 'POST' && $action === 'enregistrer') {
        require_csrf();
        $titre = trim((string)($_POST['titre'] ?? ''));
        $nouvelleImage = isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE;
        $chemin = null;
        if ($nouvelleImage) {
            $chemin = upload_image($_FILES['image'], 'oeuvre');
        } elseif ($id > 0 && $titre !== '') {
            $st = db()->prepare('SELECT image FROM oeuvres WHERE id = ?');
            $st->execute([$id]);
            $chemin = $st->fetchColumn() ?: null;
        }
        if ($titre !== '' && $chemin !== null) {
            if (true) {
                $rubrique = (int)($_POST['rubrique_id'] ?? 0);
                $valeurs = [$titre, trim((string)($_POST['artiste'] ?? '')),
                    ($_POST['annee'] ?? '') !== '' ? (int)$_POST['annee'] : null,
                    trim((string)($_POST['technique'] ?? '')), trim((string)($_POST['description'] ?? '')),
                    $chemin, $rubrique ?: null, !empty($_POST['publiee']) ? 1 : 0];
                if ($id > 0) {
                    $valeurs[] = $id;
                    db()->prepare('UPDATE oeuvres SET titre = ?, artiste = ?, annee = ?, technique = ?, description = ?, image = ?, rubrique_id = ?, publiee = ? WHERE id = ?')
                        ->execute($valeurs);
                } else {
                    $valeurs[] = now_sql();
                    db()->prepare('INSERT INTO oeuvres (titre, artiste, annee, technique, description, image, rubrique_id, publiee, date_publication) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)')
                        ->execute($valeurs);
                }
                flash('succes', 'Œuvre enregistrée.');
            }
        } else {
            flash('error', 'Le titre et l’image sont obligatoires.');
        }
        header('Location: ' . root_url('admin/oeuvres'));
        exit;
    }
    if ($method === 'POST' && $action === 'supprimer') {
        require_csrf();
        db()->prepare('DELETE FROM oeuvres WHERE id = ?')->execute([$id]);
        flash('succes', 'Œuvre supprimée.');
        header('Location: ' . root_url('admin/oeuvres'));
        exit;
    }
    if ($action === 'editer' && $id > 0 && $method === 'GET') {
        $st = db()->prepare('SELECT * FROM oeuvres WHERE id = ?');
        $st->execute([$id]);
        $editer = $st->fetch() ?: null;
        render('admin/oeuvres', ['titre' => 'Œuvres — ' . SITE_SIGLE,
            'oeuvres' => db()->query('SELECT * FROM oeuvres ORDER BY date_publication DESC')->fetchAll(),
            'rubriques' => db()->query('SELECT * FROM rubriques ORDER BY ordre, nom')->fetchAll(),
            'editer' => $editer], 'admin/layout_admin');
    }
    render('admin/oeuvres', ['titre' => 'Œuvres — ' . SITE_SIGLE,
        'oeuvres' => db()->query('SELECT * FROM oeuvres ORDER BY date_publication DESC')->fetchAll(),
        'rubriques' => db()->query('SELECT * FROM rubriques ORDER BY ordre, nom')->fetchAll(),
        'editer' => null], 'admin/layout_admin');
}

/* ---------------- Tags ---------------- */

if ($page === 'tags') {
    if ($method === 'POST' && $action === 'enregistrer') {
        require_csrf();
        $nom = trim((string)($_POST['nom'] ?? ''));
        if ($nom !== '') {
            db()->prepare(insert_ignore('INSERT OR IGNORE INTO tags (nom, slug) VALUES (?, ?)'))->execute([$nom, slugify($nom)]);
            flash('succes', 'Tag enregistré.');
        }
        header('Location: ' . root_url('admin/tags'));
        exit;
    }
    if ($method === 'POST' && $action === 'supprimer') {
        require_csrf();
        db()->prepare('DELETE FROM articles_tags WHERE tag_id = ?')->execute([$id]);
        db()->prepare('DELETE FROM tags WHERE id = ?')->execute([$id]);
        flash('succes', 'Tag supprimé.');
        header('Location: ' . root_url('admin/tags'));
        exit;
    }
    render('admin/tags', ['titre' => 'Tags — ' . SITE_SIGLE,
        'tags' => db()->query('SELECT * FROM tags ORDER BY nom')->fetchAll()], 'admin/layout_admin');
}

/* ---------------- Utilisateurs (admin seulement) ---------------- */

if ($page === 'utilisateurs') {
    if (!is_admin()) {
        flash('error', 'Seul un administrateur peut gérer les comptes.');
        header('Location: ' . root_url('admin/'));
        exit;
    }
    if ($method === 'POST' && $action === 'enregistrer') {
        require_csrf();
        $username = trim((string)($_POST['username'] ?? ''));
        $role = ($_POST['role'] ?? '') === 'admin' ? 'admin' : 'redacteur';
        $password = (string)($_POST['password'] ?? '');
        if ($username === '') {
            flash('error', 'L’identifiant est obligatoire.');
        } elseif ($id === 0 && strlen($password) < 8) {
            flash('error', 'Mot de passe : 8 caractères minimum.');
        } else {
            if ($id > 0) {
                $st = db()->prepare('SELECT id FROM utilisateurs WHERE username = ? AND id != ?');
                $st->execute([$username, $id]);
                if ($st->fetchColumn()) {
                    flash('error', 'Cet identifiant est déjà pris.');
                } elseif ($password !== '') {
                    if (strlen($password) < 8) {
                        flash('error', 'Mot de passe : 8 caractères minimum.');
                    } else {
                        db()->prepare('UPDATE utilisateurs SET username = ?, nom = ?, role = ?, bio = ?, password = ? WHERE id = ?')
                            ->execute([$username, trim((string)($_POST['nom'] ?? '')), $role, trim((string)($_POST['bio'] ?? '')), password_hash($password, PASSWORD_DEFAULT), $id]);
                    }
                } else {
                    db()->prepare('UPDATE utilisateurs SET username = ?, nom = ?, role = ?, bio = ? WHERE id = ?')
                        ->execute([$username, trim((string)($_POST['nom'] ?? '')), $role, trim((string)($_POST['bio'] ?? '')), $id]);
                }
            } else {
                $existe = db()->prepare('SELECT id FROM utilisateurs WHERE username = ?');
                $existe->execute([$username]);
                if ($existe->fetchColumn()) {
                    flash('error', 'Cet identifiant est déjà pris.');
                } else {
                    db()->prepare('INSERT INTO utilisateurs (username, password, nom, role, bio) VALUES (?, ?, ?, ?, ?)')
                        ->execute([$username, password_hash($password, PASSWORD_DEFAULT), trim((string)($_POST['nom'] ?? '')), $role, trim((string)($_POST['bio'] ?? ''))]);
                }
            }
            flash('succes', 'Compte enregistré.');
        }
        header('Location: ' . root_url('admin/utilisateurs'));
        exit;
    }
    if ($method === 'POST' && $action === 'supprimer') {
        require_csrf();
        if ($id === (int)current_user()['id']) {
            flash('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        } else {
            $admins = (int)db()->query('SELECT COUNT(*) FROM utilisateurs WHERE role = "admin"')->fetchColumn();
            $cible = db()->prepare('SELECT role FROM utilisateurs WHERE id = ?');
            $cible->execute([$id]);
            $roleCible = $cible->fetchColumn();
            if ($roleCible === 'admin' && $admins <= 1) {
                flash('error', 'Impossible : c’est le dernier administrateur.');
            } else {
                db()->prepare('DELETE FROM articles_auteurs WHERE user_id = ?')->execute([$id]);
                db()->prepare('DELETE FROM utilisateurs WHERE id = ?')->execute([$id]);
                flash('succes', 'Compte supprimé.');
            }
        }
        header('Location: ' . root_url('admin/utilisateurs'));
        exit;
    }
    if ($action === 'editer' && $id > 0 && $method === 'GET') {
        $st = db()->prepare('SELECT id, username, nom, role, bio FROM utilisateurs WHERE id = ?');
        $st->execute([$id]);
        $editer = $st->fetch() ?: null;
        render('admin/utilisateurs', ['titre' => 'Utilisateurs — ' . SITE_SIGLE,
            'personnes' => db()->query('SELECT id, username, nom, role FROM utilisateurs ORDER BY nom, username')->fetchAll(),
            'editer' => $editer], 'admin/layout_admin');
    }
    render('admin/utilisateurs', ['titre' => 'Utilisateurs — ' . SITE_SIGLE,
        'personnes' => db()->query('SELECT id, username, nom, role FROM utilisateurs ORDER BY nom, username')->fetchAll(),
        'editer' => null], 'admin/layout_admin');
}

page404('Section du back-office inconnue.');
