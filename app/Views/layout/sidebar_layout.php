<nav class="col-lg-2 sidebar-nav fixed-bottom navbar-expand-lg ">
    <div class="container-fluid">
        <div class="d-flex flex-column flex-shrink-0 p-3  offcanvas offcanvas-start" tabindex="-1" id="offCanvasSidebar" aria-labelledby="offCanvasSidebarLabel" style="width: 280px;">
            <span class="fs-4 fw-semibold ms-3-lg text-center">Menu <?= checkAuth('roleName'); ?></span>
            <hr>
            <ul class="nav nav-pills flex-column mb-auto">
                <?php foreach (getSidebarMenu() as $page) : ?>
                    <li class="nav-item d-flex flex-column w-100 py-1 my-1">
                        <a href="<?= base_url($page->page_url); ?>" class="d-flex flex-row nav-link <?= isSamePage($page->page_url) ? 'active' : 'link-dark' ?>">
                            <div class="col-2  me-1 d-flex flex-shrink-1 align-items-center"> <i class="<?= $page->page_icon; ?> me-2"></i></div>
                            <div class="col-10"><?= $page->page_title; ?></div>
                        </a>
                    </li>
                <?php endforeach ?>
            </ul>
        </div>
    </div>
</nav>
<main class=" col-lg-10 ms-lg-auto p-0">
    <?= $this->renderSection('main'); ?>

</main>
<div class="col-lg-10 ms-lg-auto mt-auto p-0">

    <?= $this->include('layout/footer'); ?>

</div>