<?= $this->extend('layout/main_template'); ?>
<?= $this->section('main'); ?>
<!-- Section 1 -->
<div class="bg-success text-white">
    <div class="container col-xxl-8 px-4 py-md-5 py-2  ">
        <div class="row flex-lg-row align-items-center g-5 py-5">
            <div class="mx-auto col-10 col-sm-8 col-lg-6">
                <iframe class="image-fluid mw-100 rounded rounded-4" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d516.8688945119997!2d107.5807391813381!3d-6.871778068674113!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68e6993756148b%3A0x9917dfe31fed46d3!2sKantor%20Kelurahan%20Sarijadi!5e0!3m2!1sid!2sid!4v1659922048808!5m2!1sid!2sid" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
            <div class="col-lg-6 text-md-start text-center">
                <h1 class=" fw-bold lh-1 mb-3">Hubungi Kami</h1>
                <p class="lead">Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptatum unde vitae ullam non beatae neque, possimus veniam, ea perspiciatis impedit nostrum eligendi voluptates provident distinctio veritatis soluta enim, dolorem architecto.</p>
            </div>
        </div>
    </div>
</div>
<!-- Section 2 -->
<div id="kirim-pesan" class="bg-white text-dark">
    <div class="container col-xxl-8 px-4 py-md-5 py-2  ">
        <div class="row flex-lg-row align-items-center g-5 py-5">
            <div class="col-lg-6 text-md-start text-center">
                <h2 class=" fw-bold lh-1 mb-3">Kritik, Saran, dan Aduan</h2>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptatum unde vitae ullam non beatae neque, possimus veniam, ea perspiciatis impedit nostrum eligendi voluptates provident distinctio veritatis soluta enim, dolorem architecto.</p>
            </div>
            <div class="col-lg-6">
                <form action="" method="post">
                    <div class="row mb-3">
                        <div class="col">
                            <div class="form-floating">
                                <input type="text" id="nama" class="form-control" placeholder="Nama" aria-label="Nama">
                                <label for="nama">Nama</label>
                            </div>
                        </div>

                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <div class="form-floating">
                                <input type="text" id="noWhatsapp" class="form-control" placeholder="No Whatsapp" aria-label="No Whatsapp">
                                <label for="noWhatsapp">No Whatsapp</label>
                            </div>

                        </div>
                        <div class="col">
                            <div class="form-floating">
                                <select class="form-select" id="jenisPesan" aria-label="Jenis Pesan">
                                    <option value="" selected>Pilih</option>
                                    <option value="kritik_saran">Kritik & Saran</option>
                                    <option value="laporan_aduan">Laporan/Aduan</option>
                                </select>
                                <label for="jenisPesan">Jenis Pesan</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <div class="form-floating">
                                <textarea style="height: 200px;" class="form-control" placeholder="Tulis pesan di sini" id="pesan"></textarea>
                                <label for="pesan">Pesan</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <div class="form-floating">
                                <button type="submit" class="btn btn-primary w-100">Kirim</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>