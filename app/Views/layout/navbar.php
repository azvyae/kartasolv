<nav class="navbar fw-semibold px-3 bg-white navbar-expand-lg shadow-sm fixed-top">
    <div class="container-md ">
        <a href="<?= base_url(); ?>" class="navbar-brand me-5">
            <img src="<?= base_url('img/logo.webp'); ?>" alt="Logo Karang Taruna" width="48" height="48">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasNavbarLabel">
                    <img src="<?= base_url('img/logo.webp'); ?>" alt="Logo Karang Taruna" width="48" height="48">
                    Navigasi
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
            </div>
            <div class="offcanvas-body align-items-center justify-content-between">
                <ul class="navbar-nav flex-grow-1 pe-3">
                    <li class="nav-item">
                        <a class="nav-link me-3 <?php if (isSamePage('Home::index')) : ?>active<?php endif ?>" href="<?= base_url(); ?>">Beranda</a>
                    </li>
                    <?php if (checkAuth('userId')) : ?>
                        <li class="nav-item">
                            <a class="nav-link me-3 <?php if (isSamePage('User\Home::index')) : ?>active<?php endif ?>" href="<?= base_url('dasbor'); ?>">Dasbor</a>
                        </li>
                    <?php endif ?>
                    <li class="nav-item">
                        <a class="nav-link me-3 <?php if (isSamePage('Home::history')) : ?>active<?php endif ?>" href="<?= base_url('sejarah'); ?>">Sejarah</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link me-3 <?php if (isSamePage('Home::contactUs')) : ?>active<?php endif ?>" href="<?= base_url('hubungi-kami'); ?>">Hubungi Kami</a>
                    </li>
                </ul>
                <ul class="navbar-nav d-flex ">
                    <?php if (checkAuth('userId')) : ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi text-secondary bi-person-circle" viewBox="0 0 16 16">
                                    <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                                    <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z" />
                                </svg>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Profil</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="#">Keluar</a></li>
                            </ul>
                        </li>
                    <?php else : ?>
                        <a href="<?= base_url('masuk'); ?>" class="btn btn-outline-secondary my-1 me-2 px-3">Masuk</a>
                        <?php if ($callToAction = getCalltoAction()) : ?>
                            <a target="<?= $callToAction->target; ?>" href="<?= $callToAction->cta_url; ?>" class="btn px-3 my-1 btn-primary me-2"><?= $callToAction->cta_text; ?></a>
                        <?php endif ?>
                    <?php endif ?>

                </ul>
            </div>
        </div>
    </div>
</nav>