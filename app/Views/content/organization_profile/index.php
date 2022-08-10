<?= $this->extend('layout/main_template'); ?>
<?= $this->section('main'); ?>
<div id="kirim-pesan" class="bg-white mt-lg-0 ms-lg-4 mt-3 h-100 text-dark">
    <div class="container-fluid col-lg-12 px-4 py-md-5 py-2 ">
        <div class="text-lg-start text-center mb-5 border-bottom ">
            <h1 class="display-4 fw-bold lh-1 mb-3">Pengaturan Profil Karta</h1>
        </div>
        <div class="row g-5 text-center py-3">
            <div class="col-md-4">
                <img class="rounded-circle shadow-lg w-50 mb-3" src="<?= base_url('img/main-info.webp'); ?>" alt="Info Karang Taruna">
                <h2><a class="link-primary text-decoration-none" href="<?= base_url('konten/profil-karang-taruna/info-utama'); ?>">Ubah Informasi Utama</a></h2>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Ullam pariatur at sint quaerat praesentium libero aliquid sit ipsa eos, possimus corporis molestias blanditiis et quas, placeat aliquam accusantium eaque delectus.</p>
            </div>
            <div class="col-md-4">
                <img class="rounded-circle shadow-lg w-50 mb-3" src="<?= base_url('img/activities.webp'); ?>" alt="Kegiatan Kami">
                <h2><a class="link-primary text-decoration-none" href="<?= base_url('konten/profil-karang-taruna/kegiatan-kami'); ?>">Ubah Kegiatan Kami</a></h2>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Ullam pariatur at sint quaerat praesentium libero aliquid sit ipsa eos, possimus corporis molestias blanditiis et quas, placeat aliquam accusantium eaque delectus.</p>
            </div>
            <div class="col-md-4">
                <img class="rounded-circle shadow-lg w-50 mb-3" src="<?= base_url('img/team.webp'); ?>" alt="Anggota">
                <h2><a class="link-primary text-decoration-none" href="<?= base_url('konten/profil-karang-taruna/pengurus'); ?>">Ubah Data Pengurus</a></h2>

                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Ullam pariatur at sint quaerat praesentium libero aliquid sit ipsa eos, possimus corporis molestias blanditiis et quas, placeat aliquam accusantium eaque delectus.</p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>