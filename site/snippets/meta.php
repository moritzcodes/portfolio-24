<style>
    @font-face {
    font-family: 'Matter';
    src: url('../assets/fonts/matter/Matter-Regular.woff2') format('woff2');
    font-weight: normal;
    font-style: normal;
}


@font-face {
    font-family: 'Matter';
    src: url('../assets/fonts/matter/Matter-SemiBold.woff2') format('woff2');
    font-weight: bold;
    font-style: normal;
}

body {
    font-family: 'Matter', 'SF Pro', 'Inter', 'Arial', sans-serif;
}
</style>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $page->title() ?> | <?= $site->title() ?></title>
<link rel="stylesheet" defer href="<?= url('assets/css/master.css') ?>">
<link rel="icon" href="<?= url('assets/images/favicon.ico') ?>" type="image/png">
<meta name="description" content="<?= $page->description() ?>">
<meta property="og:title" content="<?= $page->title() ?> | <?= $site->title() ?>">
<meta property="og:description" content="<?= $page->description() ?>">
<meta property="og:type=" content="website">
<meta property="og:image:type" content="image/png">
<script src="https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/lazysizes.min.js" async></script>


