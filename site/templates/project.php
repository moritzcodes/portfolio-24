<!DOCTYPE html>
<html lang="en">
<head>
    <?php snippet('meta') ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Video intersection observer
            const videos = document.querySelectorAll('video[data-autoplay]');
            const videoObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.play();
                    } else {
                        entry.target.pause();
                    }
                });
            }, { threshold: 0.5 });
            videos.forEach(video => videoObserver.observe(video));

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