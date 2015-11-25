<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title><?php echo $this->e($_title_.' - '.$_desc_) ?></title>

    <link href="//fonts.googleapis.com/css?family=Lato:300" rel="stylesheet" type="text/css">
    <link href="<?php echo $this->baseUrl('styles/main.css') ?>" rel="stylesheet" type="text/css">
</head>
<body>
    <?php echo $this->section('content') ?>
</body>
</html>
