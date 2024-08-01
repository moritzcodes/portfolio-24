<div class="start-content">
    <h1><?= $page->title() ?></h1>
    <p><?= $page->subtitle() ?></p> 
    <?php if (isset($gallery) && $gallery === true): ?>
        <a class="link" href="<?= url('gallery') ?>"><- Back to Gallery</a>
    <?php endif; ?>
</div>