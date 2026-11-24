<?php
/**
 * Fraction Média — requêtes de lecture (site public).
 */

function rubriques(): array {
    return db()->query('SELECT * FROM rubriques ORDER BY ordre, nom')->fetchAll();
}

function rubrique_par_slug(string $slug): ?array {
    $st = db()->prepare('SELECT * FROM rubriques WHERE slug = ?');
    $st->execute([$slug]);
    return $st->fetch() ?: null;
}

function articles_publies(int $limite = 0, int $offset = 0): array {
    $sql = 'SELECT a.*, r.nom AS rubrique_nom, r.slug AS rubrique_slug
            FROM articles a JOIN rubriques r ON r.id = a.rubrique_id
            WHERE a.statut = "publie" AND a.date_publication <= ?
            ORDER BY a.date_publication DESC';
    if ($limite > 0) {
        $sql .= ' LIMIT ' . $limite . ($offset > 0 ? ' OFFSET ' . $offset : '');
    }
    $st = db()->prepare($sql);
    $st->execute([now_sql()]);
    return $st->fetchAll();
}

function a_la_une(): ?array {
    $st = db()->prepare('SELECT a.*, r.nom AS rubrique_nom, r.slug AS rubrique_slug
        FROM articles a JOIN rubriques r ON r.id = a.rubrique_id
        WHERE a.statut = "publie" AND a.a_la_une = 1 AND a.date_publication <= ?
        ORDER BY a.date_publication DESC LIMIT 1');
    $st->execute([now_sql()]);
    return $st->fetch() ?: null;
}

function article_publie(string $slug): ?array {
    $st = db()->prepare('SELECT a.*, r.nom AS rubrique_nom, r.slug AS rubrique_slug
        FROM articles a JOIN rubriques r ON r.id = a.rubrique_id
        WHERE a.slug = ? AND a.statut = "publie" AND a.date_publication <= ?');
    $st->execute([$slug, now_sql()]);
    $article = $st->fetch();
    if ($article === null) {
        return null;
    }
    $st = db()->prepare('SELECT u.id, u.nom, u.username FROM articles_auteurs aa
        JOIN utilisateurs u ON u.id = aa.user_id WHERE aa.article_id = ?');
    $st->execute([$article['id']]);
    $article['auteurs'] = $st->fetchAll();
    $st = db()->prepare('SELECT t.* FROM articles_tags at JOIN tags t ON t.id = at.tag_id WHERE at.article_id = ?');
    $st->execute([$article['id']]);
    $article['tags'] = $st->fetchAll();
    return $article;
}

function articles_de_rubrique(int $rubriqueId): array {
    $st = db()->prepare('SELECT a.*, r.nom AS rubrique_nom, r.slug AS rubrique_slug
        FROM articles a JOIN rubriques r ON r.id = a.rubrique_id
        WHERE a.rubrique_id = ? AND a.statut = "publie" AND a.date_publication <= ?
        ORDER BY a.date_publication DESC');
    $st->execute([$rubriqueId, now_sql()]);
    return $st->fetchAll();
}

function articles_lies(int $articleId, int $rubriqueId, int $limite = 3): array {
    $st = db()->prepare('SELECT a.*, r.nom AS rubrique_nom, r.slug AS rubrique_slug
        FROM articles a JOIN rubriques r ON r.id = a.rubrique_id
        WHERE a.rubrique_id = ? AND a.id != ? AND a.statut = "publie" AND a.date_publication <= ?
        ORDER BY a.date_publication DESC LIMIT ' . $limite);
    $st->execute([$rubriqueId, $articleId, now_sql()]);
    return $st->fetchAll();
}

function galeries_publiees(int $limite = 0): array {
    $sql = 'SELECT * FROM galeries WHERE publiee = 1 ORDER BY date_publication DESC';
    if ($limite > 0) {
        $sql .= ' LIMIT ' . $limite;
    }
    $galeries = db()->query($sql)->fetchAll();
    foreach ($galeries as &$g) {
        $g['photos'] = photos_de_galerie((int)$g['id']);
    }
    unset($g);
    return $galeries;
}

function galerie_publiee(string $slug): ?array {
    $st = db()->prepare('SELECT * FROM galeries WHERE slug = ? AND publiee = 1');
    $st->execute([$slug]);
    $galerie = $st->fetch();
    if ($galerie === null) {
        return null;
    }
    $galerie['photos'] = photos_de_galerie((int)$galerie['id']);
    if ($galerie['photographe_id']) {
        $st = db()->prepare('SELECT nom, username FROM utilisateurs WHERE id = ?');
        $st->execute([$galerie['photographe_id']]);
        $galerie['photographe'] = $st->fetch() ?: null;
    } else {
        $galerie['photographe'] = null;
    }
    return $galerie;
}

function photos_de_galerie(int $galerieId): array {
    $st = db()->prepare('SELECT * FROM photos WHERE galerie_id = ? ORDER BY ordre, id');
    $st->execute([$galerieId]);
    return $st->fetchAll();
}

function oeuvres_publiees(): array {
    return db()->query('SELECT * FROM oeuvres WHERE publiee = 1 ORDER BY date_publication DESC')->fetchAll();
}

function utilisateur_par_identifiant(string $username): ?array {
    $st = db()->prepare('SELECT * FROM utilisateurs WHERE username = ?');
    $st->execute([$username]);
    return $st->fetch() ?: null;
}

function utilisateurs(): array {
    return db()->query('SELECT id, username, nom, role, bio FROM utilisateurs ORDER BY nom, username')->fetchAll();
}
