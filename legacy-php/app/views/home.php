<?php if ($une): ?>
<article class="une">
    <div class="container une-inner">
        <div class="une-text">
            <p class="kicker kicker-une">À la une · <?php echo e($une['rubrique_nom']); ?></p>
            <h1 class="une-titre"><a href="/article/<?php echo e($une['slug']); ?>/"><?php echo e($une['titre']); ?></a></h1>
            <?php if ($une['chapo']): ?><p class="une-chapo"><?php echo e($une['chapo']); ?></p><?php endif; ?>
            <p class="byline"><?php echo e(date_fr($une['date_publication'])); ?> · <?php echo e(temps_lecture($une['corps'])); ?> min de lecture</p>
        </div>
        <a class="une-media" href="/article/<?php echo e($une['slug']); ?>/">
            <?php if ($une['image']): ?>
                <img src="/<?php echo e($une['image']); ?>" alt="<?php echo e($une['legende'] ?: $une['titre']); ?>">
            <?php else: ?>
                <div class="media-fallback"><span><?php echo e(mb_substr($une['rubrique_nom'], 0, 1) ?: 'F'); ?></span></div>
            <?php endif; ?>
        </a>
    </div>
</article>
<?php endif; ?>

<section class="container section">
    <h2 class="section-titre">Derniers articles</h2>
    <div class="grid-articles">
        <?php foreach ($articles as $a) { include VIEWS_DIR . '/carte_article.php'; } ?>
        <?php if (!$articles && !$une): ?>
            <p class="empty-note">Aucun article publié pour l'instant. Les articles rédigés dans le <a href="/admin/connexion">back-office</a> apparaîtront ici.</p>
        <?php endif; ?>
    </div>
</section>

<section class="section section-photos">
    <div class="container">
        <h2 class="section-titre">Photographie</h2>
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
                <p class="empty-note empty-note-clair">Aucune galerie publiée pour l'instant.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="container section">
    <h2 class="section-titre">Art</h2>
    <div class="grid-oeuvres">
        <?php foreach ($oeuvres as $o): ?>
            <figure class="carte-oeuvre">
                <img src="/<?php echo e($o['image']); ?>" alt="<?php echo e($o['titre'] . ', ' . $o['artiste']); ?>">
                <figcaption>
                    <strong><?php echo e($o['titre']); ?></strong>
                    <span><?php echo e($o['artiste'] . ($o['annee'] ? ', ' . $o['annee'] : '')); ?></span>
                </figcaption>
            </figure>
        <?php endforeach; ?>
        <?php if (!$oeuvres): ?>
            <p class="empty-note">Aucune œuvre publiée pour l'instant.</p>
        <?php endif; ?>
    </div>
    <div class="section-lien">
        <a class="bouton" href="/art/">Voir le portfolio →</a>
    </div>
</section>
