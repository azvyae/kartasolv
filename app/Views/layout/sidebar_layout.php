<nav class="col-lg-2 sidebar-nav fixed-bottom navbar-expand-lg ">
    <div class="container-fluid">
        <div class="d-flex flex-column flex-shrink-0 p-3  offcanvas offcanvas-start" tabindex="-1" id="offCanvasSidebar" aria-labelledby="offCanvasSidebarLabel" style="width: 280px;">
            <span class="fs-4 ms-3">Menu</span>
            <hr>
            <ul class="nav nav-pills flex-column mb-auto">
                <?php foreach (getSidebarMenu() as $page) : ?>
                    <li class="nav-item py-1 my-1">
                        <a href="<?= base_url($page->page_url); ?>" class="nav-link <?= isSamePage($page->page_url) ? 'active' : 'link-dark' ?>">
                            <i class="<?= $page->page_icon; ?> me-2"></i>
                            <?= $page->page_title; ?>
                        </a>
                    </li>
                <?php endforeach ?>
            </ul>
        </div>
    </div>
</nav>
<main class="my-auto col-lg-10 ms-lg-auto p-0">
    <?= $this->renderSection('main'); ?>

</main>
<div class="col-lg-10 ms-lg-auto p-0">

    <?= $this->include('layout/footer'); ?>

</div>