<nav class="navbar shadow-sm">
    <div class="container-fluid px-5">
        <a href="<?= base_url(); ?>" class="navbar-brand">
            <img src="<?= base_url('img/logo.webp'); ?>" alt="Logo Karang Taruna" width="32" height="32">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                <a class="nav-link <?php if (isSamePage('Home/index')) : ?>active<?php endif ?>" href="<?= base_url(); ?>">Beranda</a>
                <a class="nav-link <?php if (isSamePage('Home')) : ?>active<?php endif ?>" href="#">Features</a>
                <a class="nav-link <?php if (isSamePage('Home')) : ?>active<?php endif ?>" href="#">Pricing</a>
                <a class="nav-link disabled">Disabled</a>
            </div>
        </div>
        <form class="d-flex" role="search">
            <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
            <button class="btn btn-outline-success" type="submit">Search</button>
        </form>
    </div>
</nav>