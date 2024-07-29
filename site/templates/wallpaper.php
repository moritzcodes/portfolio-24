<!DOCTYPE html>
<html lang="en">
<head>
    <?php snippet('meta') ?>
    <meta property="og:image" content="<?= e($page->template()->name() === 'wallpaper', $page->url() . '.png', url('assets/images/og-template.png')) ?>">
</head>
<body>
    <?php snippet('nav') ?>
    <main>
        <div class="wallpaper">
            <?php snippet('start') ?>
            <!-- Mac Book -->
            <div class="mockup-container">
                <img class="mockup-img no-shadow eager" src="<?= url('assets/images/wallpaper/device/light/macbook-screen-web.png') ?>" alt="MacBook Pro Mockup" class="mockup-image">
                <div class="mockup-screen mockup-macbook">
                    <?php if ($wallpaper = $page->wallpaper()->toFile()): ?>
                        <img class="no-shadow eager" src="<?= $wallpaper->thumb(['width'=> 1300,'quality'=> 70, 'format'=> 'webp'])->url() ?>" srcset="<?= $wallpaper->srcset([300, 600, 900, 1200, 1300]) ?>" alt="Screen Content">
                    <?php endif; ?>
                </div>
            </div>

            <!-- Download Card -->
            <div class="mockup-link card-item">
                <div class="item">
                    <h3>Get your wallpaper</h3>
                </div>
                <?php if ($wallpaper): ?>
                    <a href="<?= $wallpaper->mediaUrl() ?>" download class="btn">
                        Download Wallpaper
                    </a>
                <?php endif; ?>
            </div>

            <div class="mockup-grid">
                <!-- I Phone -->
                <div class="mockup-container">
                    <img class="mockup-img no-shadow" src="<?= url('assets/images/wallpaper/device/' . ($page->toggle()->toBool() ? 'dark/iphone-dark-web.png' : 'light/iphone-screen-web.png')) ?>" alt="Iphone 15 Pro Mockup" class="mockup-image">
                    <div class="mockup-screen mockup-iphone">
                        <?php if ($wallpaper): ?>
                            <img class="no-shadow" src="<?= $wallpaper->thumb(['height'=> 700,'quality'=> 40, 'format'=> 'webp'])->url() ?>" srcset="<?= $wallpaper->srcset([300, 600, 900, 1200, 1300]) ?>" alt="Screen Content">
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Apple Watch -->
                <div class="mockup-container">
                    <img class="mockup-img no-shadow" src="<?= url('assets/images/wallpaper/device/' . ($page->toggle()->toBool() ? 'dark/watch-screen-dark.png' : 'light/watch-screen-light.png')) ?>" alt="Apple Watch Ultra Mockup" class="mockup-image">
                    <div class="mockup-screen mockup-watch">
                        <?php if ($wallpaper): ?>
                            <img class="no-shadow" src="<?= $wallpaper->thumb(['height'=> 400,'quality'=> 40, 'format'=> 'webp'])->url() ?>" srcset="<?= $wallpaper->srcset([300, 600, 900, 1200, 1300]) ?>" alt="Screen Content">
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php snippet('footer') ?> 
</body>
</html>