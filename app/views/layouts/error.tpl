<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title><?php echo $this->e($title) ?></title>

    <link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Lato:300">
    <link rel="stylesheet" type="text/css" href="<?php echo $this->baseUrl('styles/main.css') ?>">
</head>
<body>
    <?php echo $this->section('content') ?>
</body>
</html>