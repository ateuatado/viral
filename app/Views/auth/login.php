<?= $this->extend(config('Auth')->views['layout']) ?>

<?= $this->section('title') ?><?= lang('Auth.login') ?> <?= $this->endSection() ?>

<?= $this->section('main') ?>

    <h5 class="text-center mb-4"><?= lang('Auth.login') ?></h5>

    <form action="<?= url_to('login') ?>" method="post">
        <?= csrf_field() ?>

        <!-- Email -->
        <div class="mb-3">
            <label for="emailInput" class="form-label"><?= lang('Auth.email') ?></label>
            <input type="email" class="form-control" id="emailInput" name="email" inputmode="email" autocomplete="email" value="<?= old('email') ?>" required>
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="passwordInput" class="form-label"><?= lang('Auth.password') ?></label>
            <input type="password" class="form-control" id="passwordInput" name="password" inputmode="text" autocomplete="current-password" required>
        </div>

        <!-- Remember me -->
        <?php if (setting('Auth.sessionConfig')['allowRemembering']): ?>
            <div class="form-check mb-4">
                <input type="checkbox" name="remember" class="form-check-input" id="rememberCheck" <?php if (old('remember')): ?> checked<?php endif ?>>
                <label class="form-check-label" for="rememberCheck">
                    <?= lang('Auth.rememberMe') ?>
                </label>
            </div>
        <?php endif; ?>

        <div class="d-grid mb-4">
            <button type="submit" class="btn btn-primary"><?= lang('Auth.login') ?></button>
        </div>

        <?php if (setting('Auth.allowMagicLinkLogins')) : ?>
            <p class="text-center mb-2"><a href="<?= url_to('magic-link') ?>" class="text-decoration-none">Esqueci minha senha</a></p>
        <?php endif ?>

        <?php if (setting('Auth.allowRegistration')) : ?>
            <p class="text-center mb-0"><?= lang('Auth.needAccount') ?> <a href="<?= url_to('register') ?>" class="text-decoration-none"><?= lang('Auth.register') ?></a></p>
        <?php endif ?>

    </form>

<?= $this->endSection() ?>
