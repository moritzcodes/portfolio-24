<!DOCTYPE html>
<html lang="en">
<head>
    <?php snippet('meta') ?>
    <meta property="og:image" content="<?= e($page->template()->name() === 'project', $page->url() . '.png', url('assets/images/og-template.png')) ?>">

</head>
<body style="position: relative">
    <div class="background-project" style="background-color: <?= $page->color() ?>"> </div>
    <?php snippet('nav') ?>
    <main>
        <?php snippet('start') ?>
        <div class="project-container">
            <?php snippet('layout', ['page' => $page]) ?>
        </div>
    </main>
    <?php snippet('footer') ?>
</body>
</html>