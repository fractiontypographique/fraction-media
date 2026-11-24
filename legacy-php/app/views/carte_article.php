<p class="kicker"><?php echo e($a['rubrique_nom']); ?></p>
<article class="carte">
    <?php if ($a['image']): ?>
        <a href="/article/<?php echo e($a['slug']); ?>/"><img class="carte-img" src="/<?php echo e($a['image']); ?>" alt="<?php echo e($a['legende'] ?: $a['titre']); ?>"></a>
    <?php endif; ?>
    <h3 class="carte-titre"><a href="/article/<?php echo e($a['slug']); ?>/"><?php echo e($a['titre']); ?></a></h3>
    <?php if ($a['chapo']): ?><p class="carte-chapo"><?php echo e($a['chapo']); ?></p><?php endif; ?>
    <p class="byline"><?php echo e(date_fr($a['date_publication'])); ?> · <?php echo e(temps_lecture($a['corps'])); ?> min</p>
</article>
