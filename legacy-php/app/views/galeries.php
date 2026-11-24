<header class="container rubrique-head">
    <h1>Photographie</h1>
    <p class="rubrique-desc">Séries photographiques publiées par la rédaction.</p>
</header>
<section class="container">
    <div class="grid-photos">
        <?php foreach ($galeries as $g): $couverture = isset($g['photos'][0]) ? $g['photos'][0] : null; ?>
            <a class="carte-photo" href="/photographie/<?php echo e($g['slug']); ?>/">
                <?php if ($couverture): ?>
                    <img src="/<?php echo e($couverture['image']); ?>" alt="<?php echo e($couverture['legende'] ?: $g['titre']); ?>">
                <?php else: ?>
                    <div class="media-fallback media-fallback-bleu"><span>◐</span></div>
                <?php endif; ?>
                <div class="carte-photo-meta">
                    <h3><?php echo e($g['titre']); ?></h3>
                    <p><?php echo count($g['photos']); ?> photographie<?php echo count($g['photos']) > 1 ? 's' : ''; ?></p>
                </div>
            </a>
        <?php endforeach; ?>
        <?php if (!$galeries): ?>
            <p class="empty-note">Aucune galerie publiée pour l'instant.</p>
        <?php endif; ?>
    </div>
</section>
