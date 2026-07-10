<?= $this->extend(config('Auth')->views['layout']) ?>

<?= $this->section('title') ?><?= lang('Auth.useMagicLink') ?> <?= $this->endSection() ?>

<?= $this->section('main') ?>

    <h5 class="text-center mb-4"><?= lang('Auth.useMagicLink') ?></h5>

    <p class="text-center mb-4"><b><?= lang('Auth.checkYourEmail') ?></b></p>

    <p class="text-center text-muted"><?= lang('Auth.magicLinkDetails', [setting('Auth.magicLinkLifetime') / 60]) ?></p>

<?= $this->endSection() ?>
