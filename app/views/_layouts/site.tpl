<?php $this->layout('layouts::base') ?>

<?php $this->start('app-content') ?>

<div class="container"><?php echo $this->section('content') ?></div>

<script src="<?php echo $this->asset('/scripts/main.js') ?>"></script>

<?php $this->stop() ?>

