<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($titre); ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="admin-body">
<header class="masthead">
    <div class="container masthead-inner">
        <a class="logo" href="/admin/"><?php echo e(SITE_SIGLE); ?> <span class="logo-accent">— back-office</span></a>
        <nav class="nav">
            <a href="/">Voir le site</a>
            <a href="/admin/deconnexion">Déconnexion</a>
        </nav>
    </div>
</header>

<div class="admin-layout">
    <aside class="admin-side">
        <a href="/admin/">Tableau de bord</a>
        <a href="/admin/articles">Articles</a>
        <a href="/admin/galeries">Galeries photo</a>
        <a href="/admin/oeuvres">Œuvres d'art</a>
        <a href="/admin/rubriques">Rubriques</a>
        <a href="/admin/tags">Tags</a>
        <?php if (is_admin()): ?><a href="/admin/utilisateurs">Utilisateurs</a><?php endif; ?>
    </aside>
    <main class="admin-main">
        <?php foreach (take_flashes() as $f): ?>
            <p class="flash flash-<?php echo e($f['type']); ?>"><?php echo e($f['message']); ?></p>
        <?php endforeach; ?>
        <?php echo $content; ?>
    </main>
</div>
</body>
</html>
