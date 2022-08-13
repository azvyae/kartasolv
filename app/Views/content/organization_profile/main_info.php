<?= $this->extend('layout/main_template'); ?>
<?= $this->section('main'); ?>
<div id="kirim-pesan" class="bg-white mt-lg-0 ms-lg-4 mt-3 h-100 text-dark">
    <div class="container-fluid col-lg-12 px-4 py-md-5 py-2 ">
        <div class="border-bottom text-lg-start text-center mb-3">
            <h1 class="display-4 fw-bold lh-1 mb-3">Ubah Informasi Utama</h1>
        </div>
        <div class="row g-5  col-lg-8 py-3">
            <form enctype="multipart/form-data" action="<?= base_url('konten/profil-karang-taruna/info-utama'); ?>" method="post">
                <?= csrf_field(); ?>
                <?= getFlash('message'); ?>
                <h2>Perubahan Info Dasar</h2>
                <div class="row ">
                    <div class="col-md-6 mb-3">
                        <div class="form-floating has-validation">
                            <input required value="<?= old('landing_title', $landing->landing_title); ?>" name="landing_title" type="text" id="landing_title" class="form-control <?= setInvalid('landing_title'); ?>" placeholder="Judul di Halaman Utama" aria-label="Judul di Halaman Utama">
                            <label for="landing_title">Judul di Halaman Utama</label>
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
                <div class="row ">
                    <div class="col-md-6 mb-3">
                        <div class="form-floating has-validation">
                            <input value="<?= old('cta_text', $landing->cta_text); ?>" name="cta_text" type="text" id="cta_text" class="form-control <?= setInvalid('cta_text'); ?>" placeholder="Teks Call to Action" aria-label="Teks Call to Action">
                            <label for="cta_text">Teks Call to Action</label>
                            <div class="invalid-feedback">
                                <?= showInvalidFeedback('cta_text'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="input-group has-validation">
                            <span class="input-group-text">https://</span>
                            <div class="form-floating <?= setInvalid('cta_url'); ?>">
                                <input value="<?= old('cta_url', removeProtocol($landing->cta_url)); ?>" name="cta_url" type="text" id="cta_url" class="form-control <?= setInvalid('cta_url'); ?>" placeholder="Url Call to Action" aria-label="Url Call to Action">
                                <label for="cta_url">Url Call to Action</label>
                            </div>
                            <div class="invalid-feedback">
                                <?= showInvalidFeedback('cta_url'); ?>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="row ">
                    <!-- Modal -->
                    <div class="modal fade" id="landing_image_modal" tabindex="-1" aria-labelledby="landing_image_modal" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Gambar di Landing Page</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <img id='preview_landing_image' class='w-100 rounded' src="<?= old('landing_image', $landing->landing_image); ?>">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3 ">
                        <label for="landing_image">Gambar di Landing</label>
                        <div class="input-group has-validation">
                            <input onchange="previewImage(this)" accept="image/*" name="landing_image" type="file" id="landing_image" class="form-control <?= setInvalid('landing_image'); ?>" placeholder="Gambar Utama di Landing" aria-label="Gambar Utama di Landing">
                            <button type="button" class="btn btn-secondary input-group-text" data-bs-toggle="modal" data-bs-target="#landing_image_modal">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-image-fill" viewBox="0 0 16 16">
                                    <path d="M.002 3a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-12a2 2 0 0 1-2-2V3zm1 9v1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V9.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12zm5-6.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0z" />
                                </svg>
                            </button>
                            <div class="invalid-feedback">
                                <?= showInvalidFeedback('landing_image'); ?>
                            </div>
                        </div>

                    </div>

                </div>
                <h2 class="mt-3">Perubahan Visi Misi</h2>
                <div class="row ">
                    <div class="col-md-12 mb-3">
                        <div class="form-floating has-validation">
                            <input required value="<?= old('vision', $landing->vision); ?>" name="vision" type="text" id="vision" class="form-control <?= setInvalid('vision'); ?>" placeholder="Visi" aria-label="Visi">
                            <label for="vision">Visi</label>
                            <div class="invalid-feedback">
                                <?= showInvalidFeedback('vision'); ?>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <div class="form-floating has-validation">
                            <textarea data-bs-toggle="tooltip" data-bs-title="Pisahkan setiap misi dengan '-' atau enter, dan gunakan tanda kurung '(...)' untuk menuliskan isi deskripsi setiap misi." style="height: 200px;" required name="mission" type="text" id="mission" class="form-control <?= setInvalid('mission'); ?>" placeholder="Misi" aria-label="Misi"><?= old('mission', parseMission($landing->mission)); ?></textarea>
                            <label for="mission">Misi</label>
                            <div class="invalid-feedback">
                                <?= showInvalidFeedback('mission'); ?>
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

<script>
    var previewImage = function(e) {
        const [file] = e.files
        preview_landing_image = document.getElementById('preview_landing_image')
        if (file) {
            preview_landing_image.src = URL.createObjectURL(file)
        } else {
            preview_landing_image.src = baseUrl('img/default.webp')
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    })
</script>


<?= $this->endSection(); ?>