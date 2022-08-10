<?= $this->extend('layout/main_template'); ?>
<?= $this->section('main'); ?>
<div id="kirim-pesan" class="bg-white mt-lg-0 ms-lg-4 mt-3 h-100 text-dark">
    <div class="container-fluid col-lg-12 px-4 py-md-5 py-2 ">
        <div class="border-bottom text-lg-start text-center mb-3">
            <h1 class="display-4 fw-bold lh-1 mb-3">Ubah Informasi Utama</h1>
        </div>
        <div class="row g-5  col-lg-8 py-3">
            <form action="<?= base_url('profil'); ?>" id="profileForm" method="post">
                <?= csrf_field(); ?>
                <?= getFlash('message'); ?>
                <h2>Perubahan Info Dasar</h2>
                <div class="row ">
                    <div class="col-md-6 mb-3">
                        <div class="form-floating has-validation">
                            <input required value="<?= old('landing_title', $landing->landing_title); ?>" name="landing_title" type="text" id="landing_title" class="form-control <?= setInvalid('landing_title'); ?>" placeholder="Judul di Halaman Utama" aria-label="Judul di Halaman Utama">
                            <label for="nalanding_titleme">Judul di Halaman Utama</label>
                            <div class="invalid-feedback">
                                <?= showInvalidFeedback('landing_title'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-floating has-validation">
                            <input required value="<?= old('landing_tagline', $landing->landing_tagline); ?>" name="landing_tagline" type="text" id="landing_tagline" class="form-control <?= setInvalid('landing_tagline'); ?>" placeholder="Tagline" aria-label="Tagline">
                            <label for="landing_tagline">Tagline</label>
                            <div class="invalid-feedback">
                                <?= showInvalidFeedback('landing_tagline'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <h2 class="mt-3">Perubahan Visi Misi</h2>
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

                    <div class="col-md-12 ">
                        <button type="submit" name="_method" value="PUT" class="btn py-2 g-recaptcha btn-primary w-100">Simpan</button>

                    </div>
                </div>

                <div class="row mb-3">

                </div>

            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>