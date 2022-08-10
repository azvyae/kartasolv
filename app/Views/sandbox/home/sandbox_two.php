<?= $this->extend('layout/main_template'); ?>
<?= $this->section('main'); ?>
<div id="kirim-pesan" class="bg-white mt-lg-0 ms-lg-4 mt-3 h-100 text-dark">
    <div class="container-fluid col-lg-12 px-4 py-md-5 py-2 ">
        <div class="border-bottom text-lg-start text-center mb-3">
            <h1 class="display-4 fw-bold lh-1 mb-3">Ubah Info</h1>
        </div>
        <div class="row g-5  col-lg-8 py-3">
            <form enctype="multipart/form-data" action="<?= base_url('sandbox'); ?>" method="post">
                <?= csrf_field(); ?>
                <?= getFlash('message'); ?>
                <h2>Perubahan Info Dasar</h2>
                <div class="row ">
                    <!-- Modal -->
                    <div class="modal fade" id="image_a_modal" tabindex="-1" aria-labelledby="image_a_modal" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="image_a_modal">Gambar</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <img id='preview_image_a' class='w-100 rounded' src="<?= old('image_a', $gbr->image_a ?? ''); ?>">
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="image_a">Gambar</label>
                        <div class="input-group has-validation">
                            <input onchange="previewImage(this)" name="image_a[]" multiple type="file" id="image_a" class="form-control <?= setInvalid('image_a'); ?>" placeholder="Gambar Utama di Landing" aria-label="Gambar Utama di Landing">
                            <div class="invalid-feedback">
                                <?= showInvalidFeedback('cta_url'); ?>
                            </div>
                            <button type="button" class="btn btn-secondary input-group-text" data-bs-toggle="modal" data-bs-target="#image_a_modal">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-image-fill" viewBox="0 0 16 16">
                                    <path d="M.002 3a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-12a2 2 0 0 1-2-2V3zm1 9v1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V9.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12zm5-6.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0z" />
                                </svg>
                            </button>
                        </div>

                        <div class="invalid-feedback">
                            <?= showInvalidFeedback('cta_text'); ?>
                        </div>
                    </div>

                </div>
                <div class="row mb-3">

                    <div class="col-md-12 ">
                        <button type="submit" name="_method" value="PUT" class="btn py-2 btn-primary w-100">Simpan</button>

                    </div>
                </div>
                <div class="row">
                    <?php d($debug) ?>

                </div>
            </form>
        </div>
    </div>
</div>

<script>
    var previewImage = function(e) {
        const [file] = e.files
        preview_image_a = document.getElementById('preview_image_a')
        if (file) {
            preview_image_a.src = URL.createObjectURL(file)
        } else {
            preview_image_a.src = baseUrl('default.webp')
        }
    }
</script>


<?= $this->endSection(); ?>