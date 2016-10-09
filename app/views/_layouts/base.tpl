<!DOCTYPE html>
<html class="no-js" lang="<?= config('lang.default') ?>">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title><?= config('app.title').' - '.config('app.description') ?></title>

    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Lato:300">
    <link rel="stylesheet" href="<?= base_url('assets/styles/main.css') ?>">
</head>
<body>

    <div id="app"><?= $this->section('page-content') ?></div>

    <?= $this->section('page-scripts') ?>

</body>
</html>
