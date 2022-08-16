<?= $this->extend('layout/main_template'); ?>
<?= $this->section('main'); ?>
<div id="kirim-pesan" class="bg-white mt-lg-0 ms-lg-4 mt-3 h-100 text-dark" style="padding-top: 64px;">
    <div class="container-fluid col-lg-12 px-4 py-md-5 py-2 ">
        <div class="border-bottom text-lg-start text-center mb-3">
            <h1 class="display-4 fw-bold lh-1 mb-3"><?= $crudType; ?></h1>
        </div>
        <div class="row g-5  col-lg-8 py-3">
            <form enctype="multipart/form-data" action="<?= base_url("data/pmks/" . ($communityId ?? 'tambah')); ?>" method="post">
                <?= csrf_field(); ?>
                <?= getFlash('message'); ?>
                <h2>Identitas PMKS</h2>
                <div class="row ">
                    <div class="col-md-6 mb-3">
                        <div class="form-floating has-validation">
                            <input required value="<?= old('community_name', $community->community_name ?? ''); ?>" name="community_name" type="text" id="community_name" class="form-control <?= setInvalid('community_name'); ?>" placeholder="Nama" aria-label="Nama">
                            <label for="community_name">Nama</label>
                            <div class="invalid-feedback">
                                <?= showInvalidFeedback('community_name'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-floating has-validation">
                            <input required value="<?= old('community_address', $community->community_address ?? ''); ?>" name="community_address" type="text" id="community_address" class="form-control <?= setInvalid('community_address'); ?>" placeholder="Alamat" aria-label="Alamat">
                            <label for="community_address">Alamat</label>
                            <div class="invalid-feedback">
                                <?= showInvalidFeedback('community_address'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row ">
                    <div class="col-md-6 mb-3">
                        <div class="form-floating has-validation">
                            <?php $pmpsksType = $community->pmpsks_type ?? '' ?>
                            <select required name="pmks_type" class="form-select <?= setInvalid('pmks_type'); ?>" id="pmks_type" aria-label="Jenis PMKS">
                                <option value="" selected>Pilih Salah Satu</option>
                                <?php foreach ($pmksTypes as $p) : ?>
                                    <option <?= set_select('pmks_type', $p->pmpsks_id, $pmpsksType === $p->pmpsks_id); ?> value="<?= $p->pmpsks_id; ?>"><?= $p->pmpsks_name; ?></option>
                                <?php endforeach ?>
                            </select>
                            <label for="pmks_type">Jenis PMKS</label>
                            <div class="invalid-feedback">
                                <?= showInvalidFeedback('pmks_type'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-floating has-validation">
                            <input value="<?= old('community_identifier', $community->community_identifier ?? ''); ?>" name="community_identifier" type="text" id="community_identifier" class="form-control <?= setInvalid('community_identifier'); ?>" placeholder="Identitas NIK/KK/Nomor" aria-label="Identitas NIK/KK/Nomor">
                            <label for="community_identifier">Identitas NIK/KK/Nomor</label>
                            <div class="invalid-feedback">
                                <?= showInvalidFeedback('community_identifier'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row ">
                    <!-- Modal -->
                    <div class="modal fade" id="foto_pmks" tabindex="-1" aria-labelledby="foto_pmks" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable modal-xl modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="foto_pmks">Foto PMKS</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body d-block text-center">
                                    <?php if ($pmpsksImg ?? false) : ?>
                                        <?php foreach ($pmpsksImg as $i) : ?>
                                            <img class='col-md-3 col-12 shadow-sm m-md-2 mb-2 rounded' src="<?= $i->pmpsks_img_loc; ?>">
                                        <?php endforeach ?>
                                    <?php else : ?>
                                        <span class="fs-4">Tidak Ada Gambar</span>
                                    <?php endif ?>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3 ">
                        <label for="pmpsks_img_loc">Foto PMKS</label>
                        <div class="input-group has-validation">
                            <input onchange="previewImage(this)" accept="image/*" multiple="multiple" name="pmpsks_img_loc[]" type="file" id="pmpsks_img_loc" class="form-control <?= setInvalid('pmpsks_img_loc'); ?>" placeholder="Foto PMKS" aria-label="Foto PMKS">
                            <button type="button" class="btn btn-secondary input-group-text" data-bs-toggle="modal" data-bs-target="#foto_pmks">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-image-fill" viewBox="0 0 16 16">
                                    <path d="M.002 3a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-12a2 2 0 0 1-2-2V3zm1 9v1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V9.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12zm5-6.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0z" />
                                </svg>
                            </button>
                            <div class="invalid-feedback">
                                <?= showInvalidFeedback('pmpsks_img_loc'); ?>
                            </div>
                        </div>

                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-check-label" for="community_status">Status Disetujui/Belum Disetujui</label>
                        <div class="form-check has-validation form-switch">
                            <?php $communityStatus = $community->community_status ?? 'Belum Disetujui' ?>
                            <input <?= set_checkbox('community_status', 'Disetujui', $communityStatus == 'Disetujui'); ?> value="Disetujui" name="community_status" style="width: 64px; height:32px" class="form-check-input" type="checkbox" role="switch" id="community_status">
                            <span class="<?= setInvalid('community_status'); ?>"></span>
                            <span class="invalid-feedback">
                                <?= showInvalidFeedback('community_status'); ?>
                            </span>


                        </div>
                    </div>
                </div>
                <div class="row ">
                    <div class="col-md-6 mb-3">
                        <a href="<?= base_url('data/pmks'); ?>" class="btn py-2 btn-outline-secondary w-100">Kembali</a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <button type="submit" <?= ($communityId ?? false) ? 'name="_method" value="PUT"' : ''; ?> class="btn py-2 btn-primary w-100">Simpan</button>

                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    var previewImage = function(e) {
        const files = e.files
        const modalBody = document.querySelector('.modal-body');
        if (files.length > 0) {
            modalBody.innerHTML = `<div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Memuat...</span>
            </div>`;
            images = '';
            for (i = 0; i < files.length; i++) {
                images += `<img class='col-md-3 shadow-sm col-12 m-md-2 mb-2 rounded' src="${URL.createObjectURL(files[i])}">`
            }
            modalBody.innerHTML = images
        } else {

            modalBody.innerHTML = `<span class="fs-4">Tidak Ada Gambar</span>`
        }
    }
</script>


<?= $this->endSection(); ?>