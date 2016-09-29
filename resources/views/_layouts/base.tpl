<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title><?= $this->e($title) ?></title>

    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Lato:300">
    <link rel="stylesheet" href="<?= base_url('assets/styles/main.css') ?>">

    <script src="<?= base_url('vendor/modernizr.js') ?>"></script>
</head>
<body>

    <div id="app"><?= $this->section('page-content') ?></div>

    <?= $this->section('page-scripts') ?>

</body>
</html>
