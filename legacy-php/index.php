<?php
/**
 * Fraction Média — point d'entrée (routeur).
 * URLs : /  /article/<slug>  /rubrique/<slug>  /photographie  /photographie/<slug>  /art  /a-propos  /rss.xml
 * (Fonctionne aussi avec un plan de ce type : /index.php?route=article/<slug>)
 */
require __DIR__ . '/app/bootstrap.php';

$route = isset($_GET['route']) ? trim((string)$_GET['route'], '/') : '';
if (isset($_SERVER['REDIRECT_QUERY_STRING'])) {
    parse_str($_SERVER['REDIRECT_QUERY_STRING'], $q);
    if (isset($q['route']) && $route === '') {
        $route = trim((string)$q['route'], '/');
    }
}
if ($route === '' && isset($_SERVER['REQUEST_URI'])) {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri = trim((string)$uri, '/');
    if ($uri !== 'index.php' && $uri !== '') {
        $route = $uri;
    }
}

if ($route === '' || $route === 'accueil' || $route === 'index.php') {
    $une = a_la_une();
    $articles = articles_publies(9);
    if ($une) {
        $articles = array_values(array_filter($articles, function ($a) use ($une) {
            return (int)$a['id'] !== (int)$une['id'];
        }));
    }
    render('home', [
        'titre' => SITE_SIGLE . ' — Art, photographie et analyse',
        'une' => $une,
        'articles' => $articles,
        'galeries' => galeries_publiees(4),
        'oeuvres' => oeuvres_publiees(),
    ]);
}

$parts = explode('/', $route);

if ($parts[0] === 'admin') {
    require __DIR__ . '/app/admin/admin.php';
    exit;
}

if ($route === 'rss.xml') { /* flux RSS */
    header('Content-Type: application/rss+xml; charset=utf-8');
    require VIEWS_DIR . '/rss.php';
    exit;
}

switch ($segments[0]) {
    case 'article':
        if (!isset($parts[1]) || $parts[1] === '') {
            page404();
        }
        $article = article_publie($parts[1]);
        if ($article === null) {
            page404('Cet article n’existe pas ou n’est pas publié.');
        }
        render('article', [
            'titre' => $article['titre'] . ' — ' . SITE_SIGLE,
            'meta' => $article['chapo'],
            'article' => $article,
            'lies' => articles_lies((int)$article['id'], (int)$article['rubrique_id']),
        ]);
        break;

    case 'rubrique':
        if (!isset($parts[1]) || $parts[1] === '') {
            page404();
        }
        $rubrique = rubrique_par_slug($parts[1]);
        if ($rubrique === null) {
            page404('Rubrique inconnue.');
        }
        render('rubrique', [
            'titre' => $rubrique['nom'] . ' — ' . SITE_SIGLE,
            'rubrique' => $rubrique,
            'articles' => articles_de_rubrique((int)$rubrique['id']),
        ]);
        break;

    case 'photographie':
        if (isset($parts[1]) && $parts[1] !== '') {
            $galerie = galerie_publiee($parts[1]);
            if ($galerie === null) {
                page404('Galerie introuvable.');
            }
            render('galerie', [
                'titre' => $galerie['titre'] . ' — Photographie — ' . SITE_SIGLE,
                'galerie' => $galerie,
            ]);
        } else {
            render('galeries', ['titre' => 'Photographie — ' . SITE_SIGLE, 'galeries' => galeries_publiees()]);
        }
        break;

    case 'art':
        render('art', ['titre' => 'Art — ' . SITE_SIGLE, 'oeuvres' => oeuvres_publiees()]);
        break;

    case 'a-propos':
        render('apropos', ['titre' => 'À propos — ' . SITE_SIGLE]);
        break;

    default:
        page404('La page demandée n’existe pas.');
}
