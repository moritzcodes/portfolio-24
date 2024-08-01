<!DOCTYPE html>
<html lang="en">
<head>
    <?php snippet('meta') ?>
    <meta property="og:image" content="<?= e($page->template()->name() === 'today', $page->url() . '.png', url('assets/images/og-template.png')) ?>">
</head>
<body>
    <?php snippet('nav') ?>
    <main>
        <div class="today">
            <?php snippet('start') ?>
            <div class="today-content">
            <table class="today-post">

                <?php foreach ($page->today()->toStructure()->sortBy('post_date', 'desc') as $post): ?>
                        <tr>
                        <td><p class="today-date"><?= date('d. F Y', strtotime($post->post_date()->value())) ?></p></td>

                            <td><h3><?= $post->title() ?></h3></td>
                        </tr>
                <?php endforeach ?>
                </table>
                
            </div>
        </div>
    </main>
    <?php snippet('footer') ?> 
</body>
</html>