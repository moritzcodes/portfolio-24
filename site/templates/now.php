<!DOCTYPE html>
<html lang="en">
<head>
    <?php snippet('meta') ?>
    <meta property="og:image" content="<?= e($page->template()->name() === 'now', $page->url() . '.png', url('assets/images/og-template.png')) ?>">
</head>
<body>
    <?php snippet('nav') ?>
    <main>
        <div class="now">
            <?php snippet('start') ?>
            <div class="now-content">
                <?php foreach ($page->posts()->toStructure()->sortBy('post_date', 'desc') as $post): ?>
                    <div class="now-post">
                        <div class="now-info sticky-item">
                            <p class="now-date"><?= date('d. F Y', strtotime($post->post_date()->value())) ?></p>
                            <p class="now-category">#<?= $post->category()->html() ?></p>
                        </div>
                        <div class="now-item">
                            <h2><?= $post->post_title() ?></h2>
                            <?php snippet('layout', ['page' => $post]); ?>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    </main>
    <?php snippet('footer') ?> 
</body>
</html>