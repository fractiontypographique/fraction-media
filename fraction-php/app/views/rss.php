<?php header('Content-Type: application/rss+xml; charset=UTF-8'); ?>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<rss version="2.0">
    <channel>
        <title><?php echo e(SITE_NOM); ?></title>
        <link>http<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 's' : ''; ?>://<?php echo e(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost'); ?>/</link>
        <description>Art, photographie et articles universitaires</description>
        <language>fr</language>
<?php foreach (articles_publies(20) as $a): ?>
        <item>
            <title><?php echo e($a['titre']); ?></title>
            <description><?php echo e($a['chapo']); ?></description>
            <link>http<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 's' : ''; ?>://<?php echo e(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost'); ?>/article/<?php echo e($a['slug']); ?>/</link>
            <guid>/article/<?php echo e($a['slug']); ?>/</guid>
            <pubDate><?php echo e(date('r', strtotime($a['date_publication']))); ?></pubDate>
        </item>
<?php endforeach; ?>
    </channel>
</rss>
