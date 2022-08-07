<?= $this->extend('layout/main_template'); ?>
<?= $this->section('main'); ?>
<!-- HERO -->
<div class="bg-secondary text-light">
    <div class="container col-xxl-8 px-4 py-md-5 py-2  ">
        <div class="row flex-lg-row-reverse align-items-center g-5 py-5">
            <div class="mx-auto col-10 col-sm-8 col-lg-6">
                <img src="<?= $members[0]->member_image; ?>" class="rounded d-block mx-lg-auto img-fluid" alt="Bootstrap Themes" width="400" height="400" loading="lazy">
            </div>
            <div class="col-lg-6 text-md-start text-center">
                <h1 class="display-5 fw-bold lh-1 mb-3"><?= $landingInfo->landing_title; ?></h1>
                <p class="lead"><?= $landingInfo->landing_tagline; ?></p>
                <?php if ($callToAction = getCalltoAction()) : ?>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                        <a target="<?= $callToAction->target; ?>" type="button" href="<?= $callToAction->cta_url; ?>" class="btn btn-primary btn-lg px-4 me-md-2"><?= $callToAction->cta_text; ?></a>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>
<!-- VISI MISI -->
<div class="bg-white text-dark">
    <div class="container col-xxl-8 px-4 py-5  ">
        <div class="row text-center">
            <h2 class="fs-1 fw-bold">Visi & Misi</h2>
            <p class="col-md-6 mx-auto"><?= $landingInfo->vision; ?></p>
        </div>
        <div class="row flex-lg-row-reverse align-items-center g-5 py-5">
            <div class="col-10 mx-auto col-sm-8 col-lg-6">
                <img src="<?= $landingInfo->landing_image; ?>" class="d-block mx-lg-auto img-fluid rounded" alt="Bootstrap Themes" width="700" height="500" loading="lazy">
            </div>
            <div class="col-lg-6 text-md-start text-center">
                <?php foreach ($data = getMissions($landingInfo->mission) as $key => $m) : ?>
                    <p class="fs-5"><?= $m['mission']; ?><br>
                        <span class="fs-6"><?= $m['desc']; ?></span>
                    </p>
                    <?php if (count($data) - 1 > $key) : ?>
                        <hr>
                    <?php endif ?>
                <?php endforeach ?>

            </div>
        </div>
    </div>
</div>
<div class="bg-info text-primary text-center">
    <div class="container col-xxl-8 px-4 py-5  ">
        <div class="row">
            <h2 class="fs-1 fw-bold">Kegiatan Kami</h2>
            <p class="col-md-6 mx-auto">Lorem ipsum dolor sit amet consectetur adipisicing elit. Eum id rerum suscipit sint saepe consequuntur eveniet eaque sed dolores maiores. Esse soluta nemo natus quos suscipit dolores laborum tempore eaque.</p>
        </div>
        <div class="row flex-lg-row align-items-center g-5 mt-md-2 py-3">
            <div class="col-10 mx-auto col-sm-8 col-lg-6">
                <img src="<?= $activitiesInfo->image_a; ?>" class="d-block mx-lg-auto shadow-lg   img-fluid rounded" alt="Bootstrap Themes" width="450" height="500" loading="lazy">
            </div>
            <div class="col-lg-6 ">
                <h3 class="fs-4"><?= $activitiesInfo->title_a; ?><br>
                </h3>
                <p class="fs-6"><?= $activitiesInfo->desc_a; ?></p>
            </div>
        </div>
        <div class="row flex-lg-row-reverse align-items-center g-5 py-3">
            <div class="col-10 mx-auto col-sm-8 col-lg-6">
                <img src="<?= $activitiesInfo->image_b; ?>" class="d-block mx-lg-auto shadow-lg img-fluid rounded" alt="Bootstrap Themes" width="450" height="500" loading="lazy">
            </div>
            <div class="col-lg-6 ">
                <h3 class="fs-4"><?= $activitiesInfo->title_b; ?><br>
                </h3>
                <p class="fs-6"><?= $activitiesInfo->desc_b; ?></p>
            </div>
        </div>
        <div class="row flex-lg-row align-items-center g-5 py-3">
            <div class="col-10 mx-auto col-sm-8 col-lg-6">
                <img src="<?= $activitiesInfo->image_c; ?>" class="d-block mx-lg-auto shadow-lg img-fluid rounded" alt="Bootstrap Themes" width="450" height="500" loading="lazy">
            </div>
            <div class="col-lg-6 ">
                <h3 class="fs-4"><?= $activitiesInfo->title_c; ?><br>
                </h3>
                <p class="fs-6"><?= $activitiesInfo->desc_c; ?></p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>