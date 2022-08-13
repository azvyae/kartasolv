<?= $this->extend('layout/main_template'); ?>
<?= $this->section('main'); ?>
<div class="bg-white mt-lg-0 ms-lg-4 mt-3 h-100 text-dark">
    <div class="container-fluid col-lg-12 px-4 py-md-5 py-2 ">
        <div class="text-lg-start text-center mb-5 border-bottom ">
            <h1 class="display-4 fw-bold lh-1 mb-3">Pengaturan Profil Karta</h1>
        </div>
        <div class="row g-5 text-center py-3">
            <div class="col-md-4">
                <img class="rounded-circle shadow-lg w-50 mb-3" src="<?= base_url('img/main-info.webp'); ?>" alt="Info Karang Taruna">
                <h2><a class="link-primary text-decoration-none" href="<?= base_url('konten/profil-karang-taruna/info-utama'); ?>">Ubah Informasi Utama</a></h2>
                <p>Ubah informasi utama, meliputi Judul utama pada halaman awal, tagline, visi misi, link Call to Action, dan foto pada bagian Visi Misi.</p>
            </div>
            <div class="col-md-4">
                <img class="rounded-circle shadow-lg w-50 mb-3" src="<?= base_url('img/activities.webp'); ?>" alt="Kegiatan Kami">
                <h2><a class="link-primary text-decoration-none" href="<?= base_url('konten/profil-karang-taruna/kegiatan-kami'); ?>">Ubah Kegiatan Kami</a></h2>
                <p>Ubah data pada bagian Kegiatan Kami di halaman utama, terdiri dari foto, judul, dan deskripsi setiap kegiatan.</p>
            </div>
            <div class="col-md-4">
                <img class="rounded-circle shadow-lg w-50 mb-3" src="<?= base_url('img/team.webp'); ?>" alt="Anggota">
                <h2><a class="link-primary text-decoration-none" href="<?= base_url('konten/profil-karang-taruna/pengurus'); ?>">Ubah Data Pengurus</a></h2>
                <p>Lihat data pengurus yang akan ditampilkan di halaman utama.</p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>