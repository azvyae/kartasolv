<?= $this->extend('layout/main_template'); ?>
<?= $this->section('main'); ?>
<div id="kirim-pesan" class="bg-white mt-lg-0 ms-lg-4 mt-3 h-100 text-dark">
    <div class="container-fluid col-lg-12 px-4 py-md-5 py-2 ">
        <div class="text-lg-start text-center mb-5 border-bottom ">
            <h1 class="display-4 fw-bold lh-1 mb-3">Dasbor <?= checkAuth('roleName'); ?></h1>
        </div>
        <div class="row g-5 text-center py-3">
            <div class="col-md-4">
                <img class="rounded-circle shadow-lg w-50 mb-3" src="<?= base_url('img/pmks.webp'); ?>" alt="PMKS">
                <h2><a class="link-primary text-decoration-none" href="<?= base_url('data/pmks'); ?>"><?= countTable('pmks'); ?> Data PMKS</a></h2>
                <p>Lihat dan kelola data Penyandang Masalah Kesejahteraan Sosial di Kelurahan Sarijadi.</p>
            </div>
            <div class="col-md-4">
                <img class="rounded-circle shadow-lg w-50 mb-3" src="<?= base_url('img/psks.webp'); ?>" alt="PSKS">
                <h2><a class="link-primary text-decoration-none" href="<?= base_url('data/psks'); ?>"><?= countTable('psks'); ?> Data PSKS</a></h2>
                <p>Lihat dan kelola data Potensi dan Sumber Kesejahteraan Sosial di Kelurahan Sarijadi.</p>
            </div>
            <div class="col-md-4">
                <img class="rounded-circle shadow-lg w-50 mb-3" src="<?= base_url('img/members.webp'); ?>" alt="Anggota">
                <h2><a class="link-primary text-decoration-none" href="<?= base_url('konten/profil-karang-taruna/pengurus'); ?>"><?= countTable('members'); ?> Pengurus Aktif</a></h2>
                <p>Lihat data pengurus aktif, dan kamu juga dapat mengelola siapa saja yang dapat tampil di halaman depan.</p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>