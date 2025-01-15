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
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Enhanced lazy loading
            if ('loading' in HTMLImageElement.prototype) {
                document.querySelectorAll('img[data-src]').forEach(img => {
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                });
            } else {
                const imageObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.src = img.dataset.src;
                            img.onload = () => img.classList.add('loaded');
                            observer.unobserve(img);
                        }
                    });
                });

                document.querySelectorAll('img[data-src]').forEach(img => {
                    imageObserver.observe(img);
                });
            }
        });
    </script>
    <style>
        .image-wrapper {
            position: relative;
            background: #f0f0f0;
            overflow: hidden;
        }
        img {
            opacity: 1;
            transition: opacity 0.3s ease-in-out;
        }
        img:not([src]) {
            opacity: 0;
        }
    </style>
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
                                    <?php foreach ($projects as $project): ?>
                                        <div class="card-item card-hover">
                                            <a href="<?= $project->url() ?>" class="projects__item-link">
                                                <article class="gallery-item">
                                                    <img class="lazy" data-src="<?= $project->wallpaper()->toFile()->thumb(['width'=> 800,'quality'=> 50,])->url() ?>" alt="Screen Content">
                                                    <p><?= $project->title() ?></p>
                                                </article>
                                            </a>
                                        </div>
                                    <?php endforeach ?>
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