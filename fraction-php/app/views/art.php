<header class="container rubrique-head">
    <h1>Art</h1>
    <p class="rubrique-desc">Le portfolio : œuvres d'artistes invité·e·s, peinture, image et arts plastiques.</p>
</header>
<section class="container">
    <div class="grid-oeuvres grille-portrait">
        <?php foreach ($oeuvres as $o): ?>
            <figure class="carte-oeuvre oeuvre-detail">
                <img src="/<?php echo e($o['image']); ?>" alt="<?php echo e($o['titre'] . ', ' . $o['artiste']); ?>" loading="lazy">
                <figcaption>
                    <strong><?php echo e($o['titre']); ?></strong>
                    <span><?php echo e($o['artiste'] . ($o['annee'] ? ', ' . $o['annee'] : '') . ($o['technique'] ? ' — ' . $o['technique'] : '')); ?></span>
                    <?php if ($o['description']): ?><p><?php echo e($o['description']); ?></p><?php endif; ?>
                </figcaption>
            </figure>
        <?php endforeach; ?>
        <?php if (!$oeuvres): ?>
            <p class="empty-note">Aucune œuvre publiée pour l'instant. <a href="/admin/connexion">Ajoutez-en dans le back-office</a>.</p>
        <?php endif; ?>
    </div>
</section>
