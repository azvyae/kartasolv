<?= $this->extend('layout/main_template'); ?>
<?= $this->section('main'); ?>
<div class="bg-white h-100 align-self-center text-dark">
    <div class="container col-xxl-8 px-4 py-md-5 py-2 ">
        <div class="row flex-lg-row align-items-center g-5 py-3">
            <div class=" text-center">
                <h1 class="display-4 fw-bold lh-1 mb-3">Masuk</h1>
            </div>

        </div>
        <div class="row flex-lg-row align-items-center g-5 pb-4">
            <form action="<?= base_url('masuk'); ?>" id="loginForm" method="post">
                <div class="row mb-3">
                    <div class="col-md-4 mx-auto">
                        <?= csrf_field(); ?>
                        <?= getFlash('message'); ?>
                        <div class="form-floating has-validation">
                            <input required value="<?= old('user_email'); ?>" name="user_email" type="email" id="email" class="form-control <?= setInvalid('user_email'); ?>" placeholder="Email" aria-label="Email">
                            <label for="email">Email</label>
                            <div class="invalid-feedback">
                                <?= showInvalidFeedback('user_email'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-1">
                    <div class="col-md-4 mx-auto">
                        <div class="form-floating has-validation">
                            <input required minlength="6" type="password" id="password" name="user_password" class="form-control <?= setInvalid('user_password'); ?>" placeholder="Kata Sandi" aria-label="Kata Sandi">
                            <label for="password">Kata Sandi</label>
                            <div class="invalid-feedback">
                                <?= showInvalidFeedback('user_password'); ?>
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
<script src="https://www.google.com/recaptcha/api.js"></script>
<script>
    function onSubmit(token) {
        form = document.getElementById("loginForm")
        if (form.reportValidity()) {
            form.submit();
        }
    }
</script>
<?= $this->endSection(); ?>