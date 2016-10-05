<?php $this->layout('layout::base') ?>

<?php $this->start('page-content') ?>

    <div class="container"><?= $this->section('content') ?></div>

<?php $this->stop() ?>

<?php $this->start('page-scripts') ?>

    <script src="<?= base_url('vendor/jquery.js') ?>"></script>
    <script src="<?= base_url('assets/scripts/main.js') ?>"></script>

<?php $this->stop() ?>

