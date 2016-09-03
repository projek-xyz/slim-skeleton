<?php $this->layout('layout::base') ?>

<?php $this->start('config-content') ?>

<div class="container"><?php echo $this->section('content') ?></div>

<script src="<?php echo $this->asset('/scripts/main.js') ?>"></script>

<?php $this->stop() ?>

