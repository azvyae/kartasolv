<div class="wrapper flex-row flex-grow-1 d-lg-flex">
    <!-- Sidebar -->
    <nav class=" navbar-expand-lg flex-column bg-opacity-10 bg-secondary" style="width: 280px;">
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
    <div class="d-flex flex-grow-1 flex-column justify-content-end">
        <main class="my-auto">
            <?= $this->renderSection('main'); ?>

        </main>
        <?= $this->include('layout/footer'); ?>

    </div>
</div>