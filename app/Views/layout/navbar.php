<script src="https://www.google.com/recaptcha/api.js"></script>
<nav class="navbar fw-semibold px-3 bg-white navbar-expand-lg shadow-sm fixed-top">
    <div class="container-fluid col-11">
        <div class="me-5">
            <a href="<?= base_url(); ?>" class="navbar-brand  <?= !isSameController(['Home', 'Auth']) ? 'd-md-inline d-none' : '' ?>">
                <img src="<?= base_url('img/logo.webp'); ?>" alt="Logo Karang Taruna" width="48" height="48">
            </a>
            <?php if (!isSameController(['Home', 'Auth'])) : ?>
                <button type="button" id="sidebarCollapse" class="navbar-toggler" data-bs-toggle="offcanvas" data-bs-target="#offCanvasSidebar" aria-controls="offCanvasSidebar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-layout-sidebar-inset" viewBox="0 0 16 16">
                        <path d="M14 2a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1h12zM2 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2H2z" />
                        <path d="M3 4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4z" />
                    </svg>
                </button>
            <?php endif ?>
        </div>


        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="offcanvas offcanvas-end" style="width: 280px;" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
            <div class="offcanvas-header border-bottom">
                <h5 class="offcanvas-title" id="offcanvasNavbarLabel">
                    <img src="<?= base_url('img/logo.webp'); ?>" alt="Logo Karang Taruna" width="48" height="48">
                    <span class="ms-2">Navigasi</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Tutup"></button>
            </div>
            <div class="offcanvas-body  align-items-center justify-content-between">
                <ul class="navbar-nav  flex-grow-1 text-end pe-3">
                    <li class="nav-item  py-2 py-md-0">
                        <a class="nav-link px-3 <?= isSamePage('/') ?>" href="<?= base_url(); ?>">Beranda</a>
                    </li>
                    <?php if (checkAuth('userId')) : ?>
                        <li class="nav-item py-2 py-md-0">
                            <a class="nav-link px-3 <?= isSamePage('dasbor') ?>" href="<?= base_url('dasbor'); ?>">Dasbor</a>
                        </li>
                    <?php endif ?>
                    <li class="nav-item py-2 py-md-0">
                        <a class="nav-link px-3 <?= isSamePage('sejarah') ?>" href="<?= base_url('sejarah'); ?>">Sejarah</a>
                    </li>
                    <li class="nav-item py-2 py-md-0">
                        <a class="nav-link px-3 <?= isSamePage('hubungi-kami') ?>" href="<?= base_url('hubungi-kami'); ?>">Hubungi Kami</a>
                    </li>
                </ul>
                <ul class="navbar-nav text-end">

                    <?php if (checkAuth('userId')) : ?>
                        <li class="nav-item dropdown py-2 py-md-0 px-3">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi text-secondary bi-person-circle" viewBox="0 0 16 16">
                                    <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                                    <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z" />
                                </svg>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?= base_url('profil'); ?>">Profil</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form id="logoutAttempt" action="<?= base_url('keluar'); ?>" method="post">
                                        <?= csrf_field(); ?>
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button data-sitekey="<?= getCaptchaSitekey(); ?>" data-callback='onLogout' data-action='logout' class="dropdown-item g-recaptcha">Keluar</button>

                                    </form>
                                    <script>
                                        function onLogout(token) {
                                            form = document.getElementById("logoutAttempt")
                                            if (form.reportValidity()) {
                                                form.submit();
                                            }
                                        }
                                    </script>
                                </li>
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