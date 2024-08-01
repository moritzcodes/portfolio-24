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
            <p>I work as a freelance product designer. I currently shift my focus in crafting 
                sustainable projects.
                Currently I’m based in Hamburg, Germany and will be moving to Stockholm, Sweden soon.</br></br>
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
                <h2>Learning Log</h2>
                <p>
                    A logbook of little things I learned, I don't learn something new every day, but I try to stay curious and always try to accumulate new knowledge on my journey through life.
                </p>
            </div>
            <div class="today-wrapper">
            <table class="today-post">
            
            <?php if ($today = page('learning-log')): ?>
                <?php $posts = $today->today()->toStructure()->sortBy('post_date', 'desc')->limit(8); ?>
                <?php foreach ($posts as $post): ?>
                        <tr>
                        <td><p class="today-date"><?= date('d. F Y', strtotime($post->post_date()->value())) ?></p></td>

                            <td><h2><?= $post->title() ?></h2></td>
                        </tr>
                <?php endforeach ?>
                <?php else: ?>
                <p>No posts found.</p>
            <?php endif ?>
                </table>
                </div>
                <div class="link-wrapper">
                <a class="link" href="<?= url('learning-log') ?>">See all my learnings</a>
                </div>
                </section>
    </main>
    <?php snippet('footer') ?>
</body>
</html>
