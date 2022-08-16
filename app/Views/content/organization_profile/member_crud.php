<?= $this->extend('layout/main_template'); ?>
<?= $this->section('main'); ?>
<div id="kirim-pesan" class="bg-white mt-lg-0 ms-lg-4 mt-3 h-100 text-dark" style="padding-top: 64px;">
    <div class="container-fluid col-lg-12 px-4 py-md-5 py-2 ">
        <div class="border-bottom text-lg-start text-center mb-3">
            <h1 class="display-4 fw-bold lh-1 mb-3"><?= $crudType; ?></h1>
        </div>
        <div class="row g-5  col-lg-8 py-3">
            <form enctype="multipart/form-data" action="<?= base_url("konten/profil-karang-taruna/pengurus/" . ($memberId ?? 'tambah')); ?>" method="post">
                <?= csrf_field(); ?>
                <?= getFlash('message'); ?>
                <h2>Perubahan Info Pengurus</h2>
                <div class="row ">
                    <div class="col-md-6 mb-3">
                        <div class="form-floating has-validation">
                            <input required value="<?= old('member_name', $member->member_name ?? ''); ?>" name="member_name" type="text" id="member_name" class="form-control <?= setInvalid('member_name'); ?>" placeholder="Nama Pengurus" aria-label="Nama Pengurus">
                            <label for="member_name">Nama Pengurus</label>
                            <div class="invalid-feedback">
                                <?= showInvalidFeedback('member_name'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-floating has-validation">
                            <input required value="<?= old('member_position', $member->member_position ?? ''); ?>" name="member_position" type="text" id="member_position" class="form-control <?= setInvalid('member_position'); ?>" placeholder="Posisi" aria-label="Posisi">
                            <label for="member_position">Posisi</label>
                            <div class="invalid-feedback">
                                <?= showInvalidFeedback('member_position'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row ">
                    <div class="col-md-6 mb-3">
                        <div class="form-floating has-validation">
                            <?php $memberType = $member->member_type ?? '' ?>
                            <select name="member_type" class="form-select <?= setInvalid('member_type'); ?>" id="member_type" aria-label="Jenis Pengurus">
                                <option value="" selected>Pilih Salah Satu</option>
                                <option <?= set_select('member_type', '1', $memberType === '1'); ?> value="1">Ketua</option>
                                <option <?= set_select('member_type', '2', $memberType === '2'); ?> value="2">Top Level</option>
                                <option <?= set_select('member_type', '3', $memberType === '3'); ?> value="3">Kabid</option>
                                <option <?= set_select('member_type', '4', $memberType === '4'); ?> value="4">Anggota</option>
                            </select>
                            <label for="member_type">Jenis Pengurus</label>
                            <div class="invalid-feedback">
                                <?= showInvalidFeedback('member_type'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">

                        <label class="form-check-label" for="member_active">Aktif?</label>
                        <div class="form-check has-validation form-switch">
                            <?php $memberActive = $member->member_active ?? 'Nonaktif' ?>
                            <input style="width: 48px; height:24px" <?= set_checkbox('member_active', 'Aktif', $memberActive == 'Aktif'); ?> value="Aktif" name="member_active" class="form-check-input" type="checkbox" role="switch" id="member_active">
                            <span class="<?= setInvalid('member_active'); ?>"></span>
                            <span class="invalid-feedback">
                                <?= showInvalidFeedback('member_active'); ?>
                            </span>

                        </div>
                    </div>
                </div>
                <div class="row ">
                    <!-- Modal -->
                    <div class="modal fade" id="foto_pengurus" tabindex="-1" aria-labelledby="foto_pengurus" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="foto_pengurus">Foto Pengurus</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <img onerror="this.setAttribute('src',baseUrl('img/default.webp'))" id='preview_member_image' class='w-100 rounded' src="<?= old('member_image', $member->member_image ?? ''); ?>">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3 ">
                        <label for="member_image">Foto Pengurus</label>
                        <div class="input-group has-validation">
                            <input onchange="previewImage(this)" accept="image/*" name="member_image" type="file" id="member_image" class="form-control <?= setInvalid('member_image'); ?>" placeholder="Foto Pengurus" aria-label="Foto Pengurus">
                            <button type="button" class="btn btn-secondary input-group-text" data-bs-toggle="modal" data-bs-target="#foto_pengurus">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-image-fill" viewBox="0 0 16 16">
                                    <path d="M.002 3a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-12a2 2 0 0 1-2-2V3zm1 9v1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V9.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12zm5-6.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0z" />
                                </svg>
                            </button>
                            <div class="invalid-feedback">
                                <?= showInvalidFeedback('member_image'); ?>
                            </div>
                        </div>

                    </div>

                </div>
                <div class="row ">
                    <div class="col-md-6 mb-3">
                        <a href="<?= base_url('konten/profil-karang-taruna/pengurus'); ?>" class="btn py-2 btn-outline-secondary w-100">Kembali</a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <button type="submit" <?= ($memberId ?? false) ? 'name="_method" value="PUT"' : ''; ?> class="btn py-2 btn-primary w-100">Simpan</button>

                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    var previewImage = function(e) {
        const [file] = e.files
        preview_member_image = document.getElementById('preview_member_image')
        if (file) {
            preview_member_image.src = URL.createObjectURL(file)
        } else {
            preview_member_image.src = baseUrl('img/default.webp')
        }
    }
</script>


<?= $this->endSection(); ?>