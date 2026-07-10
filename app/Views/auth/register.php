<?= $this->extend(config('Auth')->views['layout']) ?>

<?= $this->section('title') ?><?= lang('Auth.register') ?> <?= $this->endSection() ?>

<?= $this->section('main') ?>

    <h5 class="text-center mb-4"><?= lang('Auth.register') ?></h5>

    <form action="<?= url_to('register') ?>" method="post">
        <?= csrf_field() ?>

        <!-- Email -->
        <div class="mb-3">
            <label for="emailInput" class="form-label"><?= lang('Auth.email') ?></label>
            <input type="email" class="form-control" id="emailInput" name="email" inputmode="email" autocomplete="email" value="<?= old('email') ?>" required>
        </div>

        <!-- Username -->
        <div class="mb-3">
            <label for="usernameInput" class="form-label"><?= lang('Auth.username') ?></label>
            <input type="text" class="form-control" id="usernameInput" name="username" inputmode="text" autocomplete="username" value="<?= old('username') ?>" required>
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="passwordInput" class="form-label"><?= lang('Auth.password') ?></label>
            <input type="password" class="form-control" id="passwordInput" name="password" inputmode="text" autocomplete="new-password" required>
        </div>

        <!-- Password (Again) -->
        <div class="mb-4">
            <label for="passwordConfirmInput" class="form-label"><?= lang('Auth.passwordConfirm') ?></label>
            <input type="password" class="form-control" id="passwordConfirmInput" name="password_confirm" inputmode="text" autocomplete="new-password" required>
        </div>

        <div class="d-grid mb-4">
            <button type="submit" class="btn btn-primary"><?= lang('Auth.register') ?></button>
        </div>

        <p class="text-center mb-0"><?= lang('Auth.haveAccount') ?> <a href="<?= url_to('login') ?>" class="text-decoration-none"><?= lang('Auth.login') ?></a></p>

    </form>

<?= $this->endSection() ?>
