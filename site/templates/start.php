<!DOCTYPE html>
<html lang="en">
<head>
    <?php snippet('meta') ?>
    <meta property="og:image" content="<?= url('assets/images/og-template.png') ?>">
    
</head>
<body>
    <?php snippet('nav') ?>
    <main>
    <section class="intro">
            <p><?= $page->intro()->kirbytext() ?></br></br>
            </p>

            <p>You can contact me via 
                <div class="intro-sustainable tooltip-container-text tooltip-container tooltip-mail"> 
                    <div class="tooltip-wrapper">   
                    <div class="tooltip">
                        <p>Drop me a line, always open to chat  </p>
                    </div>
                    </div>
                        <a href="mailto:<?= $page->email() ?>">Mail</a>
                    </div> 
                    or
                    <div class="intro-sustainable tooltip-container-text tooltip-container tooltip-twitter">    
                        <div class="tooltip">
                            <p>Twitter, still the right name</p>
                        </div>
                            <a href="<?= $page->twitter() ?>" target="_blank" rel="noopener noreferrer">Twitter</a>
                        </div>
                        and
                        <div class="intro-sustainable tooltip-container-text tooltip-container tooltip-readcv">    
                        <div class="tooltip">
                            <p>CV + Chat</p>
                        </div>
                        <a href="<?= $page->readcv() ?>" target="_blank" rel="noopener noreferrer">read.cv</a>
                    
                </div>
            </p>
        </section>
        <section class="projects">
            <?php if ($work = page('work')): ?>
            <?php foreach ($work->children() as $project): ?>
                <a href="<?= $project->url() ?>" class="projects__item-link">
                    <article class="projects-item">
                        <div class="projects-image" style="background-color: <?= $project->color() ?>">
                            <?php if($project->cover()->toFile()): ?>
                                <img
                                    class="lazy" data-src="<?= $project->cover()->toFile()->url() ?>" alt="<?= $project->title() ?>">
                            <?php endif ?>
                        </div>
                        <h2><?= $project->title() ?> <span><?= $project->subtitle() ?></span></h2>
                        <p><?= $project->description()->kirbytext() ?></p>
                    </article>
                </a>
            <?php endforeach ?>
            <?php else: ?>
                <p>No projects found.</p>
            <?php endif ?>
        </section>

        <section class="today-content">
            <div class="today-start">
                <h2>Playground Feed</h2>
                <p>
                    A little playgroud where I learn new stuff or just create a little demo.
                </p>
            </div>
            <div class="playground-grid">
            <a href="<?= url('gallery') ?>">
                <div class="card-item card-hover">
                    <div class="card-header">
                        <h2>Wallpaper</h2>
                        <p>Download curated high-quality photos taken by me</p>
                    </div>
                    <?php if ($gallery = page('gallery')): ?>
                        <?php if ($images = $gallery->children()->first()): ?>
                            <?php if ($image = $images->image()->toFile()): ?>
                                <div class="mockup-container">
                                    <img class="mockup-img no-shadow lazy" data-src="<?= url('assets/images/wallpaper/device/light/macbook-screen-web.png') ?>" alt="MacBook Pro Mockup" class="mockup-image">
                                    <div class="mockup-screen mockup-macbook">
                                    <img class="lazy" data-src="<?= $images->wallpaper()->toFile()->thumb(['width'=> 600,'quality'=> 50,])->url() ?>" alt="Screen Content">
                                        <?php endif; ?>
                                    </div>
                                </div>     
                        <?php endif ?>
                    <?php else: ?>
                        <p>Gallery page not found.</p>
                    <?php endif ?>
                </div>
            </a>
            <a href="<?= url('learning-log') ?>">
                <div class="card-item card-hover">
                    <div class="card-header">
                        <h2>Learning Log</h2>
                        <p>A logbook of little things I learned over time</p>
                    </div>
                <div class="today-wrapper">
                    <table class="today-post">
                    <?php if ($today = page('learning-log')): ?>
                        <?php $posts = $today->today()->toStructure()->sortBy('post_date', 'desc')->limit(3); ?>
                        <?php foreach ($posts as $post): ?>
                                <tr>
                                    <td><h3><?= $post->title() ?></h3></td>
                                </tr>
                        <?php endforeach ?>
                        <?php else: ?>
                        <p>No posts found.</p>
                    <?php endif ?>
                        </table>
                    </div>
                </div>
            </a>
            </div>
                </section>
    </main>
    <?php snippet('footer') ?>
</body>
</html>