<?= $this->extend('layout/main_template'); ?>
<?= $this->section('main'); ?>
<div id="kirim-pesan" class="bg-white mt-lg-0 ms-lg-4 mt-3 h-100 text-dark" style="padding-top: 64px;">
    <div class="container-fluid col-lg-12 px-4 py-md-5 py-2 ">
        <div class="border-bottom text-lg-start text-center mb-3">
            <h1 class="display-4 fw-bold lh-1 mb-3">Ubah Info Sejarah</h1>
        </div>
        <div class="row g-5  col-lg-8 py-3">
            <form enctype="multipart/form-data" action="<?= base_url('konten/sejarah'); ?>" method="post">
                <?= csrf_field(); ?>
                <?= getFlash('message'); ?>
                <div class="row ">
                    <div class="col-md-6 mb-3">
                        <div class="form-floating has-validation">
                            <input required value="<?= old('title_a', $history->title_a); ?>" name="title_a" type="text" id="title_a" class="form-control <?= setInvalid('title_a'); ?>" placeholder="Judul Bagian 1" aria-label="Judul Bagian 1">
                            <label for="title_a">Judul Bagian 1</label>
                            <div class="invalid-feedback">
                                <?= showInvalidFeedback('title_a'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-floating has-validation">
                            <input required value="<?= old('desc_a', $history->desc_a); ?>" name="desc_a" type="text" id="desc_a" class="form-control <?= setInvalid('desc_a'); ?>" placeholder="Deskripsi Bagian 1" aria-label="Deskripsi Bagian 1">
                            <label for="desc_a">Deskripsi Bagian 1</label>
                            <div class="invalid-feedback">
                                <?= showInvalidFeedback('desc_a'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row ">
                    <div class="col-md-6 mb-3">
                        <div class="form-floating has-validation">
                            <input required value="<?= old('title_b', $history->title_b); ?>" name="title_b" type="text" id="title_b" class="form-control <?= setInvalid('title_b'); ?>" placeholder="Judul Bagian 2" aria-label="Judul Bagian 2">
                            <label for="title_b">Judul Bagian 2</label>
                            <div class="invalid-feedback">
                                <?= showInvalidFeedback('title_b'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-floating has-validation">
                            <input required value="<?= old('desc_b', $history->desc_b); ?>" name="desc_b" type="text" id="desc_b" class="form-control <?= setInvalid('desc_b'); ?>" placeholder="Deskripsi Bagian 2" aria-label="Deskripsi Bagian 2">
                            <label for="desc_b">Deskripsi Bagian 2</label>
                            <div class="invalid-feedback">
                                <?= showInvalidFeedback('desc_b'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row ">
                    <div class="col-md-6 mb-3">
                        <div class="form-floating has-validation">
                            <input required value="<?= old('title_c', $history->title_c); ?>" name="title_c" type="text" id="title_c" class="form-control <?= setInvalid('title_c'); ?>" placeholder="Judul Bagian 3" aria-label="Judul Bagian 3">
                            <label for="title_c">Judul Bagian 3</label>
                            <div class="invalid-feedback">
                                <?= showInvalidFeedback('title_c'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-floating has-validation">
                            <input required value="<?= old('desc_c', $history->desc_c); ?>" name="desc_c" type="text" id="desc_c" class="form-control <?= setInvalid('desc_c'); ?>" placeholder="Deskripsi Bagian 3" aria-label="Deskripsi Bagian 3">
                            <label for="desc_c">Deskripsi Bagian 3</label>
                            <div class="invalid-feedback">
                                <?= showInvalidFeedback('desc_c'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row ">
                    <div class="col-md-6 mb-3">
                        <div class="form-floating has-validation">
                            <input required value="<?= old('title_d', $history->title_d); ?>" name="title_d" type="text" id="title_d" class="form-control <?= setInvalid('title_d'); ?>" placeholder="Judul Bagian 4" aria-label="Judul Bagian 4">
                            <label for="title_d">Judul Bagian 4</label>
                            <div class="invalid-feedback">
                                <?= showInvalidFeedback('title_d'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-floating has-validation">
                            <input required value="<?= old('desc_d', $history->desc_d); ?>" name="desc_d" type="text" id="desc_d" class="form-control <?= setInvalid('desc_d'); ?>" placeholder="Deskripsi Bagian 4" aria-label="Deskripsi Bagian 4">
                            <label for="desc_d">Deskripsi Bagian 4</label>
                            <div class="invalid-feedback">
                                <?= showInvalidFeedback('desc_d'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6 mb-3 ">
                        <label for="image_a">Gambar 1</label>
                        <div class="input-group has-validation">
                            <input onchange="previewImage(this)" accept="image/*" name="image_a" type="file" id="image_a" class="form-control <?= setInvalid('image_a'); ?>" placeholder="Gambar 1" aria-label="Gambar 1">
                            <button data-bs-imageurl="<?= old('image_a', $history->image_a); ?>" id="image_a_button" type="button" data-bs-title="Gambar 1" class="btn btn-secondary input-group-text" data-bs-toggle="modal" data-bs-target="#image_modal">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-image-fill" viewBox="0 0 16 16">
                                    <path d="M.002 3a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-12a2 2 0 0 1-2-2V3zm1 9v1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V9.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12zm5-6.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0z" />
                                </svg>
                            </button>
                            <div class="invalid-feedback">
                                <?= showInvalidFeedback('image_a'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3 ">
                        <label for="image_a">Gambar 2</label>
                        <div class="input-group has-validation">
                            <input onchange="previewImage(this)" accept="image/*" name="image_b" type="file" id="image_b" class="form-control <?= setInvalid('image_b'); ?>" placeholder="Gambar 2" aria-label="Gambar 2">
                            <button data-bs-imageurl="<?= old('image_b', $history->image_b); ?>" id="image_b_button" type="button" data-bs-title="Gambar 2" class="btn btn-secondary input-group-text" data-bs-toggle="modal" data-bs-target="#image_modal">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-image-fill" viewBox="0 0 16 16">
                                    <path d="M.002 3a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-12a2 2 0 0 1-2-2V3zm1 9v1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V9.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12zm5-6.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0z" />
                                </svg>
                            </button>
                            <div class="invalid-feedback">
                                <?= showInvalidFeedback('image_b'); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">

                    <div class="col-md-12 ">
                        <button type="submit" name="_method" value="PUT" class="btn py-2 btn-primary w-100">Simpan</button>

                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="image_modal" tabindex="-1" aria-labelledby="image_modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="image_modal">Gambar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <img class='w-100 rounded' src="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<script>
    const imageModal = document.getElementById('image_modal')
    imageModal.addEventListener('show.bs.modal', event => {
        // Button that triggered the modal
        const button = event.relatedTarget
        // Extract info from data-bs-* attributes
        const src = button.getAttribute('data-bs-imageurl')
        const title = button.getAttribute('data-bs-title')
        // If necessary, you could initiate an AJAX request here
        // and then do the updating in a callback.
        //
        // Update the modal's content.
        const modalTitle = imageModal.querySelector('.modal-title')
        const modalBodyInput = imageModal.querySelector('.modal-body img')

        modalTitle.textContent = title
        modalBodyInput.setAttribute('src', src)
    })
    var previewImage = function(e) {
        const [file] = e.files
        imageId = e.getAttribute('id');
        image_x_button = document.getElementById(`${imageId}_button`)
        if (file) {
            image_x_button.setAttribute('data-bs-imageurl', URL.createObjectURL(file))
        } else {
            image_x_button.setAttribute('data-bs-imageurl', baseUrl('img/default.webp'))
        }
    }
</script>


<?= $this->endSection(); ?>