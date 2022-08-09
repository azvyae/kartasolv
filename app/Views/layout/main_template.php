<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="<?= base_url('css/main.min.css'); ?>" rel="stylesheet">
    <link href="<?= base_url('css/style.css'); ?>" rel="stylesheet">
    <meta name="description" content="Sebagai organisasi sosial kepemudaan, Karang Taruna Ngajomantara Kelurahan Sarijadi merupakan wadah pembinaan dan pengembangan serta pemberdayaan dalam upaya mengembangkan kegiatan ekonomi produktif dengan pendayagunaan semua potensi yang tersedia di lingkungan baik sumber daya manusia maupun sumber daya alam yang telah ada untuk meningkatkan kesejahteraan sosial. Karang taruna melakukan kegiatan kegiatan pengembangan potensi dengan harapan tercapainya masyarakat yang yang baik dan tepat sasaran sesuai kebutuhan masyarakat.">
    <meta name="keywords" content="karang taruna, ngajomantara, kelurahan sarijadi, pelayanan sosial, <?= $title; ?>">
    <link rel="shortcut icon" type="image/x-icon" href="<?= base_url('favicon.ico'); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <title><?= $title; ?></title>
</head>

<body class="d-flex flex-column min-vh-100">
    <header style="margin-bottom: 64px;">
        <?= $this->include('layout/navbar'); ?>
    </header>
    <?php if ($sidebar ?? false) : ?>
        <?= $this->include('layout/sidebar_layout'); ?>
    <?php else : ?>
        <?= $this->include('layout/normal_layout'); ?>
    <?php endif ?>
</body>

</html>