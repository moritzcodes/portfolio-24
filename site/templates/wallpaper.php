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
                <img class="mockup-img no-shadow" src="<?= url('assets/images/wallpaper/device/light/macbook-screen-web.png') ?>" alt="MacBook Pro Mockup" class="mockup-image">
            <div class="mockup-screen mockup-macbook">
                <?php
                
                $wallpaper = $page->wallpaper()->toFile();
                $wallpaper->srcset([
                    300  => '0.4x',
                    600  => '0.5x',
                    900  => '0.8x',
                    1200 => '1x',
                    1300 => '1.5x'
                  ]);
                if($wallpaper): ?>
                    <img class="no-shadow" src="<?= $wallpaper->thumb(['width'=> 1300,'quality'=> 70, 'format'=> 'webp'])->url() ?>" srcset="<?= $wallpaper->srcset([300, 600, 900, 1200, 1300]) ?>" alt="Screen Content">
                <?php endif; ?>
            </div>
            </div>

            <!-- Download Card -->
            <div class="mockup-link card-item">
                <div class="item">
                    <h3>Get your wallpaper</h3>
                </div>
                <?php
                    $wallpaper = $page->wallpaper()->toFile();
                    
                    $image = $page->images()
                 
                ?>
                <a href="<?= $wallpaper->mediaUrl() ?>" download class="btn">
                    Download Wallpaper
                </a>

            </div>


            <div class="mockup-grid">
                <!-- I Phone -->
                <div class="mockup-container">
                    <?php if (!$page->toggle()->toBool()): ?>
                        <img class="mockup-img no-shadow" src="<?= url('assets/images/wallpaper/device/light/iphone-screen-web.png') ?>" alt="Iphone 15 Pro Mockup" class="mockup-image">
                    <?php else: ?>
                        <img class="mockup-img no-shadow" src="<?= url('assets/images/wallpaper/device/dark/iphone-dark-web.png') ?>" alt="Iphone 15 Pro Mockup" class="mockup-image">
                    <?php endif; ?>
                            
                    <div class="mockup-screen mockup-iphone">
                        <?php
                        $wallpaper = $page->wallpaper()->toFile();
                        if($wallpaper): ?>
                            <img class="no-shadow" src="<?= $wallpaper->thumb(['height'=> 700,'quality'=> 40, 'format'=> 'webp'])->url() ?>" srcset="<?= $wallpaper->srcset([300, 600, 900, 1200, 1300]) ?>" alt="Screen Content">
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Apple Watch -->
                <div class="mockup-container">
                    <?php if (!$page->toggle()->toBool()): ?>
                        <img class="mockup-img no-shadow" src="<?= url('assets/images/wallpaper/device/light/watch-screen-light.png') ?>" alt="Apple Watch Ultra Mockup" class="mockup-image">
                    <?php else: ?>
                        <img class="mockup-img no-shadow" src="<?= url('assets/images/wallpaper/device/dark/watch-screen-dark.png') ?>" alt="Apple Watch Ultra Mockup" class="mockup-image">
                    <?php endif; ?>
                            
                    <div class="mockup-screen mockup-watch">
                        <?php
                        $wallpaper = $page->wallpaper()->toFile();
                        if($wallpaper): ?>
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