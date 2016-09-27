<?php $this->layout('layout::base') ?>

<?php $this->start('config-content') ?>

<div class="container"><?= $this->section('content') ?></div>

<script src="<?= base_url('/scripts/main.js') ?>"></script>

<?php $this->stop() ?>

