<article class="article-page">
    <header class="container article-head">
        <a class="kicker" href="/rubrique/<?php echo e($article['rubrique_slug']); ?>/"><?php echo e($article['rubrique_nom']); ?></a>
        <h1><?php echo e($article['titre']); ?></h1>
        <?php if ($article['chapo']): ?><p class="article-chapo"><?php echo e($article['chapo']); ?></p><?php endif; ?>
        <p class="byline">
            <?php if ($article['auteurs']): ?>Par <?php $noms = []; foreach ($article['auteurs'] as $au) { $noms[] = e($au['nom'] ?: $au['username']); } echo implode(', ', $noms); ?> · <?php endif; ?>
            <time datetime="<?php echo e($article['date_publication']); ?>"><?php echo e(date_fr($article['date_publication'])); ?></time> ·
            <?php echo e(temps_lecture($article['corps'])); ?> min de lecture
        </p>
    </header>

    <?php if ($article['image']): ?>
    <figure class="article-couverture">
        <img src="/<?php echo e($article['image']); ?>" alt="<?php echo e($article['legende'] ?: $article['titre']); ?>">
        <?php if ($article['legende']): ?><figcaption class="container"><?php echo e($article['legende']); ?></figcaption><?php endif; ?>
    </figure>
    <?php endif; ?>

    <div class="container article-corps">
        <?php echo markdown($article['corps']); ?>
    </div>

    <?php if ($article['tags']): ?>
    <aside class="container article-tags">
        <?php foreach ($article['tags'] as $t): ?>
            <span class="tag">#<?php echo e($t['nom']); ?></span>
        <?php endforeach; ?>
    </aside>
    <?php endif; ?>
</article>

<?php if ($lies): ?>
<section class="container section">
    <h2 class="section-titre">Dans la même rubrique</h2>
    <div class="grid-articles">
        <?php foreach ($lies as $a) { include VIEWS_DIR . '/carte_article.php'; } ?>
    </div>
</section>
<?php endif; ?>
