<header class="container rubrique-head">
    <h1><?php echo e($rubrique['nom']); ?></h1>
    <?php if ($rubrique['description']): ?><p class="rubrique-desc"><?php echo e($rubrique['description']); ?></p><?php endif; ?>
</header>
<section class="container">
    <div class="grid-articles">
        <?php foreach ($articles as $a) { include VIEWS_DIR . '/carte_article.php'; } ?>
        <?php if (!$articles): ?>
            <p class="empty-note">Aucun article publié dans cette rubrique pour l'instant.</p>
        <?php endif; ?>
    </div>
</section>
