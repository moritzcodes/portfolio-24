<!DOCTYPE html>
<html lang="en">
<head>
    <?php snippet('meta') ?>
    <meta property="og:image" content="<?= e($page->template()->name() === 'default', $page->url() . '.png', url('assets/images/og-template.png')) ?>">
</head>
<body>
    <?php snippet('nav') ?>
    <main>
        <?php snippet('start') ?>
        <div class="blocks-section">
            <?= $page->blocks()->toBlocks() ?>
        </div>
    </main>
    <?php snippet('footer') ?>
</body>
</html>
