<header class="container rubrique-head">
    <a class="kicker" href="/photographie/">← Toutes les galeries</a>
    <h1><?php echo e($galerie['titre']); ?></h1>
    <?php if ($galerie['description']): ?><p class="rubrique-desc"><?php echo e($galerie['description']); ?></p><?php endif; ?>
    <p class="byline">
        <?php if ($galerie['photographe']): ?>Photographies de <?php echo e($galerie['photographe']['nom'] ?: $galerie['photographe']['username']); ?> · <?php endif; ?>
        <?php echo e(date_fr($galerie['date_publication'])); ?>
    </p>
</header>
<section class="container galerie-grille">
    <?php foreach ($galerie['photos'] as $p): ?>
        <figure class="photo-item">
            <img src="/<?php echo e($p['image']); ?>" alt="<?php echo e($p['legende'] ?: $galerie['titre']); ?>" loading="lazy">
            <?php if ($p['legende'] || $p['credit']): ?>
                <figcaption><?php echo e($p['legende']); ?><?php if ($p['credit']): ?> — <?php echo e($p['credit']); ?><?php endif; ?></figcaption>
            <?php endif; ?>
        </figure>
    <?php endforeach; ?>
    <?php if (!$galerie['photos']): ?>
        <p class="empty-note">Cette galerie ne contient pas encore de photographies.</p>
    <?php endif; ?>
</section>
