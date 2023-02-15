<?= $this->extend('layout/main_template') ?>
<?= $this->section('main') ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />
<!-- HERO -->
<div class="hero bg-warning text-dark" style="padding-top: 64px;">
    <div class="col-xxl-8 py-md-5 container px-4 py-2">
        <div class="row flex-lg-row-reverse align-items-center g-5 py-5">
            <div class="col-10 col-sm-8 col-lg-6 mx-auto">
                <img src="<?= base_url('img/logo.webp') ?>"
                    class="col-8 col-lg-8 d-block mx-lg-auto img-fluid mx-auto rounded" alt="Bootstrap Themes"
                    width="350" height="350" loading="lazy">
            </div>
            <div class="col-lg-6 text-md-start text-center">
                <h1 class="display-4 fw-bold lh-1 mb-3">
                    <?= $landingInfo->landing_title ?>
                </h1>
                <p class="lead">
                    <?= $landingInfo->landing_tagline ?>
                </p>
                <?php if ($callToAction = getCalltoAction()): ?>
                    <div class="d-grid d-md-flex justify-content-md-start gap-2">
                        <a target="<?= $callToAction->target ?>" type="button" href="<?= $callToAction->cta_url ?>"
                            class="btn btn-primary btn-lg me-md-2 px-4"><?= $callToAction->cta_text ?></a>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>
<!-- VISI MISI -->
<div class="text-dark bg-white">
    <div class="col-xxl-8 container px-4 py-5">
        <div class="row text-center">
            <h2 class="display-4 fw-bold">Visi & Misi</h2>
            <p class="col-md-8 fs-6 mx-auto">
                <?= $landingInfo->vision ?>
            </p>
        </div>
        <div class="row flex-lg-row-reverse align-items-center g-5 py-5">
            <div class="col-10 col-sm-8 col-lg-6 mx-auto">
                <img src="<?= $landingInfo->landing_image ?>" class="d-block mx-lg-auto img-fluid rounded"
                    alt="Bootstrap Themes" width="700" height="500" loading="lazy">
            </div>
            <div class="col-lg-6 text-md-start text-center">
                <?php foreach ($data = getMissions($landingInfo->mission) as $key => $m): ?>
                    <p class="fs-5">
                        <?= $m['mission'] ?><br>
                        <span class="fs-6">
                            <?= $m['desc'] ?>
                        </span>
                    </p>
                    <?php if (count($data) - 1 > $key): ?>
                        <hr>
                    <?php endif ?>
                <?php endforeach ?>

            </div>
        </div>
    </div>
</div>
<!-- Kegiatan Kami -->
<div class="bg-info bg-gradient text-primary text-center shadow-lg">
    <div class="col-xxl-8 container px-4 py-5">
        <div class="row">
            <h2 class="display-4 fw-bold">Kegiatan Kami</h2>
            <p class="col-md-8 fs-6 mx-auto">Ada serangkaian dan berbagai macam jenis kegiatan yang dilakukan oleh
                Karang Taruna Kelurahan Sarijadi, khususnya untuk menjalankan program kerja yang ada, yang memiliki
                berbagai macam jenis kegiatan.</p>
        </div>
        <div class="row flex-lg-row align-items-center g-5 mt-md-2 py-3">
            <div class="col-10 col-sm-8 col-lg-6 mx-auto">
                <img src="<?= $activitiesInfo->image_a ?>" class="d-block mx-lg-auto img-fluid rounded shadow-lg"
                    alt="Bootstrap Themes" width="450" height="500" loading="lazy">
            </div>
            <div class="col-lg-6">
                <h3 class="fs-3 fw-bold col-lg-10 mx-auto">
                    <?= $activitiesInfo->title_a ?><br>
                </h3>
                <p class="fs-6 col-lg-8 mx-auto">
                    <?= $activitiesInfo->desc_a ?>
                </p>
            </div>
        </div>
        <div class="row flex-lg-row-reverse align-items-center g-5 py-3">
            <div class="col-10 col-sm-8 col-lg-6 mx-auto">
                <img src="<?= $activitiesInfo->image_b ?>" class="d-block mx-lg-auto img-fluid rounded shadow-lg"
                    alt="Bootstrap Themes" width="450" height="500" loading="lazy">
            </div>
            <div class="col-lg-6">
                <h3 class="fs-3 fw-bold col-lg-10 mx-auto">
                    <?= $activitiesInfo->title_b ?><br>
                </h3>
                <p class="fs-6 col-lg-8 mx-auto">
                    <?= $activitiesInfo->desc_b ?>
                </p>
            </div>
        </div>
        <div class="row flex-lg-row align-items-center g-5 py-3">
            <div class="col-10 col-sm-8 col-lg-6 mx-auto">
                <img src="<?= $activitiesInfo->image_c ?>" class="d-block mx-lg-auto img-fluid rounded shadow-lg"
                    alt="Bootstrap Themes" width="450" height="500" loading="lazy">
            </div>
            <div class="col-lg-6">
                <h3 class="fs-3 fw-bold col-lg-10 mx-auto">
                    <?= $activitiesInfo->title_c ?><br>
                </h3>
                <p class="fs-6 col-lg-8 mx-auto">
                    <?= $activitiesInfo->desc_c ?>
                </p>
            </div>
        </div>
    </div>
</div>
<div class="bg-warning bg-gradient text-primary text-center shadow-lg">
    <div class="col-xxl-10 container px-4 py-5">
        <div class="row">
            <h2 class="display-4 fw-bold">Berita</h2>
            <p class="col-md-8 fs-6 mx-auto">Cek berita terbaru dari Kartasarijadi Press.</p>
        </div>
        <div class="row flex-lg-row align-items-center g-5 py-3">
            <?php if (count($posts) > 0): ?>
                <?php foreach ($posts as $post): ?>
                    <div class="col-10 col-sm-8 col-lg-4 mx-auto">
                        <a href="<?= $post->link ?>">
                            <img style="object-fit: cover; width: 400px; height:250px" src="<?= $post->media ?>"
                                class="d-block mx-lg-auto img-fluid rounded shadow-lg" alt="<?= $post->title ?>" loading="lazy">
                        </a>
                        <a href="<?= $post->link ?>" class="text-decoration-none">
                            <h2 class="fs-4 fw-bold col-lg-10 mx-auto mt-4">
                                <?= $post->title ?>
                            </h2>
                        </a>
                        <p class="fs-6 mx-auto">
                            <?= $post->excerpt ?>
                        </p>
                        <div class="d-flex justify-content-around">
                            <span>
                                <?= $post->date ?>
                            </span>
                            <a href="<?= $post->link ?>" class="small">
                                Lihat Selengkapnya
                            </a>
                        </div>
                    </div>
                <?php endforeach ?>
            <?php else: ?>
                <span class="fs-3">Belum ada postingan</span>
            <?php endif ?>
        </div>
    </div>
</div>
<!-- Siapa Kami -->
<div class="text-dark bg-white">
    <div class="col-xxl-8 container px-4 py-5">
        <div class="row text-center">
            <h2 class="display-4 fw-bold">Siapa Kami</h2>
            <p class="col-md-8 fs-6 mx-auto">Kami tidak sendiri, kami bergerak bersama, Warga Kelurahan Sarijadi dan
                para Pemudanya. Kami adalah Karang Taruna Ngajomantara.</p>
        </div>
        <div class="row flex-lg-row align-items-center g-5 py-lg-5 py-2">
            <div class="swiper">
                <!-- Additional required wrapper -->
                <div class="swiper-wrapper">
                    <!-- Slides -->
                    <?php foreach ($members as $m): ?>
                        <?php $urlFallback = 'https://avatars.dicebear.com/api/initials/' . rawurlencode($m->member_name) . '.svg?background=%234F4F4F&fontSize=35&bold=true'; ?>
                        <div class="swiper-slide text-center">
                            <img onerror="imageFallbackOption(this, '<?= $urlFallback ?>')" src="<?= $m->member_image ?>"
                                class="rounded-circle mb-4" width="150px" height="150px"
                                style="object-fit: cover; object-position: top" alt="Foto <?= $m->member_name ?>">
                            <p class="fw-semibold col-lg-12 col-10 fs-5 mx-auto mb-1">
                                <?= $m->member_name ?>
                            </p>
                            <p class="fs-6 col-lg-12 col-10 mx-auto">
                                <?= $m->member_position ?>
                            </p>
                        </div>
                    <?php endforeach ?>

                </div>
                <div class="swiper-pagination d-none"></div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const navbar = document.querySelector('#main-navbar');
        hero = document.querySelector('.hero')
        height = Math.max(hero.scrollHeight, hero.offsetHeight,
            hero.clientHeight, hero.scrollHeight, hero.offsetHeight);
        window.onscroll = () => {
            if (window.scrollY > height) {
                navbar.classList.add('bg-white');
                navbar.classList.remove('bg-warning');
                navbar.classList.add('shadow-sm');
            } else {
                navbar.classList.remove('bg-white');
                navbar.classList.add('bg-warning');
                navbar.classList.remove('shadow-sm');

            }
        };
    })
    var imageFallbackOption = (el, url) => {
        el.setAttribute("src", url);
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
<script src="<?= base_url('js/swiper.min.js') ?>"></script>
<?= $this->endSection() ?>
