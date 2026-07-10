<?= $this->extend(config('Auth')->views['layout']) ?>

<?= $this->section('title') ?><?= lang('Auth.useMagicLink') ?> <?= $this->endSection() ?>

<?= $this->section('main') ?>

    <h5 class="text-center mb-4"><?= lang('Auth.useMagicLink') ?></h5>

    <form action="<?= url_to('magic-link') ?>" method="post">
        <?= csrf_field() ?>

        <!-- Email -->
        <div class="mb-4">
            <label for="emailInput" class="form-label"><?= lang('Auth.email') ?></label>
            <input type="email" class="form-control" id="emailInput" name="email" autocomplete="email" placeholder="<?= lang('Auth.email') ?>" value="<?= old('email', auth()->user()->email ?? null) ?>" required>
        </div>

        <div class="d-grid mb-4">
            <button type="submit" class="btn btn-primary"><?= lang('Auth.send') ?></button>
        </div>

        <p class="text-center mb-0"><a href="<?= url_to('login') ?>" class="text-decoration-none"><?= lang('Auth.backToLogin') ?></a></p>
    </form>

<?= $this->endSection() ?>
