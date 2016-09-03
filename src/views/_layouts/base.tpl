<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title><?= $this->e($title) ?></title>

    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Lato:300">
    <link rel="stylesheet" type="text/css" href="<?= $this->asset('/styles/main.css') ?>">
</head>
<body>

    <div id="app"><?= $this->section('config-content') ?></div>

</body>
</html>
