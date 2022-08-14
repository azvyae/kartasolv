<?= $this->extend('layout/main_template'); ?>
<?= $this->section('main'); ?>
<?php $request = service('request') ?>
<div class="bg-white h-100 align-self-center text-dark" style="padding-top: 64px;">
    <div class="container col-xxl-8 px-4 py-md-5 py-2 ">
        <div class="row flex-lg-row align-items-center g-5 py-3">
            <div class=" text-center">
                <h1 class="display-4 fw-bold lh-1 mb-3">Atur Ulang Kata Sandi</h1>
            </div>

        </div>
        <div class="row flex-lg-row align-items-center g-5 pb-4">
            <form action="<?= base_url('atur-ulang-kata-sandi?uuid=' . $request->getGet('uuid') . '&attempt=' . $request->getGet('attempt')); ?>" id="resetPasswordForm" method="post">
                <div class="row mb-3">
                    <div class="col-md-4 mx-auto">
                        <?= csrf_field(); ?>
                        <?= getFlash('message'); ?>
                        <div class="form-floating has-validation">
                            <input onkeyup="clearInput();" required minlength="6" type="password" id="user_new_password" name="user_new_password" class="form-control <?= setInvalid('user_new_password'); ?>" placeholder="Kata Sandi Baru" aria-label="Kata Sandi Baru">
                            <label for="password">Kata Sandi Baru</label>
                            <div class="invalid-feedback">
                                <?= showInvalidFeedback('user_new_password'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-1">
                    <div class="col-md-4 mx-auto">
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
                        <input type="hidden" name="_method" value="PUT">
                        <button data-sitekey="<?= getCaptchaSitekey(); ?>" data-callback='onSubmit' data-action='reset_password' class="btn g-recaptcha btn-primary w-100">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    var check = function() {
        if (document.getElementById('user_new_password').value ==
            document.getElementById('password_verify').value) {
            document.getElementById('user_new_password').classList.remove("is-invalid");
            document.getElementById('password_verify').classList.remove("is-invalid");
            document.getElementById('verify-message').innerHTML = '';
            return true
        } else {
            document.getElementById('user_new_password').classList.add("is-invalid");
            document.getElementById('password_verify').classList.add("is-invalid");
            document.getElementById('verify-message').innerHTML = 'Kata Sandi Harus Sama!';
            return false
        }
    }
    var clearInput = function() {
        document.getElementById('user_new_password').classList.remove("is-invalid");
        document.getElementById('password_verify').classList.remove("is-invalid");
        document.getElementById('verify-message').innerHTML = '';
    }

    function onSubmit(token) {
        form = document.getElementById("resetPasswordForm")
        if (form.reportValidity() && check()) {
            form.submit();
        }
    }
</script>
<?= $this->endSection(); ?>