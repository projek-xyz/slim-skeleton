<?php $this->layout('layout::site') ?>

<h1 id="heading">Hello, <?= $this->e($name) ?></h1>
<div id="content"><?= config('app.description') ?></div>
