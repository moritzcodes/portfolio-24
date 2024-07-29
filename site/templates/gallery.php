<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        @font-face {
        font-family: 'Nanum Pen';
        src: url('../assets/fonts/nanum/NanumPenScript-Regular.woff2') format('woff2');
        font-weight: normal;
        font-style: normal;
    }
    </style>
    <?php snippet('meta') ?>
    <meta property="og:image" content="<?= e($page->template()->name() === 'gallery', $page->url() . '.png', url('assets/images/og-template.png')) ?>">
</head>
<body>
    <?php snippet('nav') ?>
    <main>
        <div class="gallery">
            <?php snippet('start') ?>



                <?php if ($gallery = page('gallery')): ?>
                    <?php 
                    $tagsArray = [];
                    foreach ($gallery->children() as $project): 
                        if ($tags = $project->tags()):
                            foreach ($tags->split() as $tag):
                                $tagsArray[$tag][] = $project;
                            endforeach;
                        endif;
                    endforeach;
                    ?>
                    <?php foreach ($tagsArray as $tag => $projects): ?>
                        <section class="gallery-section">
                            <h2><?= $tag ?></h2>
                            <div class="gallery-grid">
                                <div class="card-item card-hover">
                                    <?php foreach ($projects as $project): ?>
                                        <a href="<?= $project->url() ?>" class="projects__item-link">
                                            <article class="gallery-item">
                                                <img src="<?= $project->wallpaper()->toFile()->thumb(['width'=> 800,'quality'=> 50,])->url() ?>" alt="Screen Content">
                                                <p><?= $project->title() ?></p>
                                            </article>
                                        </a>
                                    <?php endforeach ?>
                                </div>
                            </div>
                        </section>
                    <?php endforeach ?>
                <?php else: ?>
                    <p>No wallpapers found.</p>
                <?php endif ?>
        </div>
    </main>
    <?php snippet('footer') ?> 
</body>
</html> 