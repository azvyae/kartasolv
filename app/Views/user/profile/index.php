<?= $this->extend('layout/main_template'); ?>
<?= $this->section('main'); ?>
<div id="kirim-pesan" class="bg-white mt-lg-0 ms-lg-4 mt-3 h-100 text-dark">
    <div class="container-fluid col-lg-12 px-4 py-md-5 py-2 ">
        <div class="border-bottom text-lg-start text-center mb-3">
            <h1 class="display-4 fw-bold lh-1 mb-3">Ubah Akun/Profil</h1>

        </div>
        <div class="row g-5  col-lg-8 py-3">
            <form action="<?= base_url('masuk'); ?>" id="loginForm" method="post">
                <?= csrf_field(); ?>
                <?= getFlash('message'); ?>
                <h2>Perubahan Data Pribadi</h2>
                <div class="row ">
                    <div class="col-md-6 mb-3">
                        <div class="form-floating has-validation">
                            <input required value="<?= old('user_name'); ?>" name="user_name" type="text" id="name" class="form-control <?= setInvalid('user_name'); ?>" placeholder="Nama Lengkap" aria-label="Nama Lengkap">
                            <label for="name">Nama Lengkap</label>
                            <div class="invalid-feedback">
                                <?= showInvalidFeedback('user_name'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">

                        <div class="form-floating has-validation">
                            <input required value="<?= old('user_email'); ?>" name="user_email" type="email" id="email" class="form-control <?= setInvalid('user_email'); ?>" placeholder="Email" aria-label="Email">
                            <label for="email">Email</label>
                            <div class="invalid-feedback">
                                <?= showInvalidFeedback('user_email'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <h2 class="mt-3">Perubahan Kata Sandi</h2>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="form-floating has-validation">
                            <input required minlength="6" type="password" id="old_password" name="user_old_password" class="form-control <?= setInvalid('user_old_password'); ?>" placeholder="Kata Sandi Lama" aria-label="Kata Sandi Lama">
                            <label for="old_password">Kata Sandi Lama</label>
                            <div class="invalid-feedback">
                                <?= showInvalidFeedback('user_old_password'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="form-floating has-validation">
                            <input onkeyup="clearInput();" required minlength="6" type="password" id="password" name="user_password" class="form-control <?= setInvalid('user_password'); ?>" placeholder="Kata Sandi Baru" aria-label="Kata Sandi Baru">
                            <label for="password">Kata Sandi Baru</label>
                            <div class="invalid-feedback">
                                <?= showInvalidFeedback('user_password'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-floating has-validation">
                            <input onkeyup="clearInput()" required minlength="6" type="password" id="password_verify" name="password_verify" class="form-control <?= setInvalid('password_verify'); ?>" placeholder="Verifikasi Kata Sandi Baru" aria-label="Verifikasi Kata Sandi Baru">
                            <label for="password_verify">Verifikasi Kata Sandi Baru</label>
                            <div id="verify-message" class="invalid-feedback">
                                <?= showInvalidFeedback('password_verify'); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 mx-auto ">
                        <span class="text-muted  <?= setInvalid('g-recaptcha-response'); ?> small">Situs ini dilindungi oleh reCAPTCHA dan berlaku
                            <a class="text-decoration-none" href="https://policies.google.com/privacy">Kebijakan Privasi</a> dan
                            <a class="text-decoration-none" href="https://policies.google.com/terms">Persyaratan Layanan</a> Google.
                        </span>
                        <div class="invalid-feedback">
                            <?= showInvalidFeedback('g-recaptcha-response'); ?>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4 mx-auto">
                        <button data-sitekey="<?= getCaptchaSitekey(); ?>" data-callback='onSubmit' data-action='submit' class="btn g-recaptcha btn-primary w-100">Masuk</button>

                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 mx-auto text-center">
                        <a href="<?= base_url('lupa-kata-sandi'); ?>" class="text-decoration-none">Lupa Kata Sandi</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>