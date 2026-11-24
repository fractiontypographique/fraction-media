<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($titre); ?></title>
    <meta name="description" content="<?php echo e(isset($meta) && $meta ? $meta : SITE_NOM . ' : un média entre art, photographie et articles universitaires — politique, sociologie, histoire.'); ?>">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' fill='%23e5383b'/><text x='50' y='72' font-size='64' font-family='Georgia' text-anchor='middle' fill='white'>F</text></svg>">
    <?php if (isset($rss) && $rss): ?>
    <link rel="alternate" type="application/rss+xml" title="<?php echo e(SITE_NOM); ?>" href="/rss.xml">
    <?php endif; ?>
</head>
<body>
<header class="masthead">
    <div class="container masthead-inner">
        <a class="logo" href="/"><?php echo e(SITE_SIGLE); ?></a>
        <button class="nav-toggle" aria-label="Ouvrir le menu" aria-expanded="false">☰</button>
        <nav class="nav" id="site-nav">
            <?php foreach (rubriques() as $r): ?>
                <a href="/rubrique/<?php echo e($r['slug']); ?>/"><?php echo e($r['nom']); ?></a>
            <?php endforeach; ?>
            <a href="/photographie/">Photographie</a>
            <a href="/art/">Art</a>
            <a href="/a-propos/">À propos</a>
        </nav>
    </div>
</header>

<main>
    <?php echo $content; ?>
</main>

<footer class="footer">
    <div class="container footer-inner">
        <div>
            <p class="footer-logo"><?php echo e(SITE_SIGLE); ?></p>
            <p class="footer-tagline">Un média entre art, photographie et analyse universitaire.</p>
        </div>
        <nav class="footer-nav">
            <?php foreach (rubriques() as $r): ?>
                <a href="/rubrique/<?php echo e($r['slug']); ?>/"><?php echo e($r['nom']); ?></a>
            <?php endforeach; ?>
            <a href="/photographie/">Photographie</a>
            <a href="/art/">Art</a>
            <a href="/a-propos/">À propos</a>
        </nav>
        <p class="footer-contribute">
            <?php if (current_user() !== null): ?>
                Connecté·e : <?php $u = current_user(); echo e($u['nom'] !== '' ? $u['nom'] : $u['username']); ?> — <a href="/admin/">back-office</a>
            <?php else: ?>
                Rédaction : <a href="/admin/connexion">accès rédaction</a>
            <?php endif; ?>
            · <a href="/rss.xml">flux RSS</a>
        </p>
    </div>
    <div class="container footer-legal">
        <p>© <?php echo date('Y'); ?> <?php echo e(SITE_NOM); ?> — tous droits réservés.</p>
    </div>
</footer>

<script src="/assets/js/nav.js" defer></script>
</body>
</html>
