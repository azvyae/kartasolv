<?= $this->extend('layout/main_template'); ?>
<?= $this->section('main'); ?>
<div id="kirim-pesan" class="bg-white mt-lg-0 ms-lg-4 mt-3 h-100 text-dark" style="padding-top: 64px;">
    <div class="container-fluid col-lg-12 px-4 py-md-5 py-2 ">
        <div class="border-bottom text-lg-start text-center mb-3">
            <h1 class="display-4 fw-bold lh-1 mb-3">Tambah Data Dengan Sheet</h1>
        </div>
        <div class="row g-5  col-lg-8 py-3">
            <form enctype="multipart/form-data" action="<?= base_url("data/psks/tambah-spreadsheet"); ?>" method="post">
                <?= csrf_field(); ?>
                <?= getFlash('message'); ?>
                <h2>Form PSKS Menggunakan Spreadsheet</h2>
                <div class="row ">
                    <div class="col-md-12 mb-3 ">
                        <label for="file_excel">Upload File Excel</label>
                        <div class="input-group has-validation">
                            <input onchange="previewImage(this)" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" name="file_excel" type="file" id="file_excel" class="form-control <?= setInvalid('file_excel'); ?>" placeholder="Foto PSKS" aria-label="Foto PSKS">
                            <div class="invalid-feedback">
                                <?= showInvalidFeedback('file_excel'); ?>
                            </div>
                        </div>

                    </div>

                </div>
                <div class="row ">
                    <div class="col-md-6 mb-3">
                        <a href="<?= base_url('data/psks'); ?>" class="btn py-2 btn-outline-secondary w-100">Kembali</a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <button type="submit" class="btn py-2 btn-primary w-100">Simpan</button>

                    </div>
                </div>

            </form>
        </div>
    </div>
</div>


<?= $this->endSection(); ?>