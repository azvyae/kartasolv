<?= $this->extend('layout/main_template'); ?>
<?= $this->section('main'); ?>
<div id="kirim-pesan" class="bg-white mt-lg-0 ms-lg-4 mt-3 h-100 text-dark">
    <div class="container-fluid col-lg-12 px-4 py-md-5 py-2 ">
        <div class="border-bottom text-lg-start text-center mb-3">
            <h1 class="display-4 fw-bold lh-1 mb-3">Ubah Akun/Profil</h1>
        </div>
        <div class="row g-5  col-lg-8 py-3">
            <form action="<?= base_url('profil'); ?>" id="profileForm" method="post">
                <?= csrf_field(); ?>
                <input type="hidden" name="user_email" value="<?= $user->user_email; ?>">
                <?= getFlash('message'); ?>
                <h2>Perubahan Data Pribadi</h2>
                <div class="row ">
                    <div class="col-md-6 mb-3">
                        <div class="form-floating has-validation">
                            <input required value="<?= old('user_name', $user->user_name); ?>" name="user_name" type="text" id="name" class="form-control <?= setInvalid('user_name'); ?>" placeholder="Nama Lengkap" aria-label="Nama Lengkap">
                            <label for="name">Nama Lengkap</label>
                            <div class="invalid-feedback">
                                <?= showInvalidFeedback('user_name'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">

                        <div class="form-floating has-validation">
                            <input required value="<?= old('user_temp_mail', $user->user_email); ?>" name="user_temp_mail" type="email" id="email" class="form-control <?= setInvalid('user_temp_mail'); ?>" placeholder="Email" aria-label="Email">
                            <label for="email">Email</label>
                            <div class="invalid-feedback">
                                <?= showInvalidFeedback('user_temp_mail'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <h2 class="mt-3">Perubahan Kata Sandi</h2>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="form-floating has-validation">
                            <input onkeyup="changePasswordCondition();" minlength="6" type="password" id="user_password" name="user_password" class="form-control <?= setInvalid('user_password'); ?>" placeholder="Kata Sandi Lama" aria-label="Kata Sandi Lama">
                            <label for="user_password">Kata Sandi Lama</label>
                            <div class="invalid-feedback">
                                <?= showInvalidFeedback('user_password'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="form-floating has-validation">
                            <input disabled onkeyup="clearInput();" minlength="6" type="password" id="user_new_password" name="user_new_password" class="form-control <?= setInvalid('user_new_password'); ?>" placeholder="Kata Sandi Baru" aria-label="Kata Sandi Baru">
                            <label for="password">Kata Sandi Baru</label>
                            <div class="invalid-feedback">
                                <?= showInvalidFeedback('user_new_password'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-floating has-validation">
                            <input disabled onkeyup="clearInput()" minlength="6" type="password" id="password_verify" name="password_verify" class="form-control <?= setInvalid('password_verify'); ?>" placeholder="Verifikasi Kata Sandi Baru" aria-label="Verifikasi Kata Sandi Baru">
                            <label for="password_verify">Verifikasi Kata Sandi Baru</label>
                            <div id="verify-message" class="invalid-feedback">
                                <?= showInvalidFeedback('password_verify'); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6 mb-3">
                        <span class="text-muted  <?= setInvalid('g-recaptcha-response'); ?> small">Situs ini dilindungi oleh reCAPTCHA dan berlaku
                            <a class="text-decoration-none" href="https://policies.google.com/privacy">Kebijakan Privasi</a> dan
                            <a class="text-decoration-none" href="https://policies.google.com/terms">Persyaratan Layanan</a> Google.
                        </span>
                        <div class="invalid-feedback">
                            <?= showInvalidFeedback('g-recaptcha-response'); ?>
                        </div>
                    </div>
                    <div class="col-md-6 ">
                        <input type="hidden" name="_method" value="PUT">
                        <button data-sitekey="<?= getCaptchaSitekey(); ?>" data-callback='onSubmit' data-action='submit' class="btn py-2 g-recaptcha btn-primary w-100">Simpan</button>

                    </div>
                </div>

                <div class="row mb-3">

                </div>

            </form>
        </div>
    </div>
</div>
<script src="https://www.google.com/recaptcha/api.js"></script>
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

    var changePasswordCondition = function() {
        if (document.getElementById('user_password').value) {
            document.getElementById('user_password').setAttribute('required', 'required')
            document.getElementById('user_new_password').setAttribute('required', 'required')
            document.getElementById('password_verify').setAttribute('required', 'required')
            document.getElementById('user_new_password').removeAttribute('disabled')
            document.getElementById('password_verify').removeAttribute('disabled')
        } else {
            document.getElementById('user_new_password').setAttribute('disabled', 'disabled')
            document.getElementById('password_verify').setAttribute('disabled', 'disabled')
            document.getElementById('user_new_password').value = ''
            document.getElementById('password_verify').value = ''
            document.getElementById('user_password').removeAttribute('required')
            document.getElementById('user_new_password').removeAttribute('required')
            document.getElementById('password_verify').removeAttribute('required')
        }
    }

    function onSubmit(token) {
        form = document.getElementById("profileForm")
        if (form.reportValidity() && check()) {
            form.submit();
        }
    }
</script>
<?= $this->endSection(); ?>