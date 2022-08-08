<?= $this->extend('layout/main_template'); ?>
<?= $this->section('main'); ?>
<div id="kirim-pesan" class="bg-white h-100 align-self-center text-dark">
    <div class="container col-xxl-8 px-4 py-md-5 py-2 ">
        <div class="row flex-lg-row align-items-center g-5 py-3">
            <div class=" text-center">
                <h1 class="display-4 fw-bold lh-1 mb-3">Lupa Kata Sandi</h1>
            </div>

        </div>
        <div class="row flex-lg-row align-items-center g-5 pb-4">
            <form action="" method="post">
                <div class="row mb-3">
                    <div class="col-md-4 mx-auto">
                        <div class="form-floating">
                            <input type="email" id="email" class="form-control" placeholder="Email" aria-label="Email">
                            <label for="email">Email</label>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 mx-auto">
                        <div class="form-floating">
                            <input type="password" id="password" class="form-control" placeholder="Kata Sandi" aria-label="Kata Sandi">
                            <label for="password">Kata Sandi</label>
                        </div>
                    </div>

                </div>

                <div class="row mb-3">
                    <div class="col-md-4 mx-auto">
                        <div class="form-floating">
                            <button type="submit" class="btn btn-primary w-100">Atur Ulang Kata Sandi</button>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4 mx-auto">
                        <div class="form-floating text-center ">
                            <a href="<?= base_url('masuk'); ?>" class="text-decoration-none">Kembali ke Halaman Login</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>