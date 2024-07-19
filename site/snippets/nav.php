<header>
    <div class="logo">
        <a href="/">
            <div class="logo-image">    
            </div>
        </a>
        <a href="/">
            <div class="profile-info">
                <h1><?= $site->title() ?></h1>
                <h2>Web Designer</h2>
            </div>
        </a>
    </div>
    <nav>
        <ul class="nav-links">
        <?php
            $pages = $site->children()->listed();
            if ($pages->count() === 0) {
                echo '<li>No pages found</li>';
            } else {
                foreach($pages as $item): ?>
                    <li>
                        <a class="link" href="<?= $item->url() ?>" class="<?= $item->isOpen() ? 'active' : '' ?>">
                            <?= $item->title()->html() ?>
                        </a>
                    </li>
                <?php endforeach;
            }
            ?>
        </ul>
    </nav>
</header>