<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="<?= base_url('css/main.min.css'); ?>" rel="stylesheet">
    <meta name="description" content="Sebagai organisasi sosial kepemudaan, Karang Taruna Ngajomantara Kelurahan Sarijadi merupakan wadah pembinaan dan pengembangan serta pemberdayaan dalam upaya mengembangkan kegiatan ekonomi produktif dengan pendayagunaan semua potensi yang tersedia di lingkungan baik sumber daya manusia maupun sumber daya alam yang telah ada untuk meningkatkan kesejahteraan sosial. Karang taruna melakukan kegiatan kegiatan pengembangan potensi dengan harapan tercapainya masyarakat yang yang baik dan tepat sasaran sesuai kebutuhan masyarakat.">
    <meta name="keywords" content="karang taruna, ngajomantara, kelurahan sarijadi, pelayanan sosial, <?= $title; ?>">
    <link rel="shortcut icon" type="image/x-icon" href="<?= base_url('favicon.ico'); ?>">
    <title><?= $title; ?></title>
</head>

<body>
    <header>
        <?= $this->include('layout/navbar'); ?>
    </header>
    <main>
        <?= $this->renderSection('main'); ?>
    </main>
    <footer>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    </footer>
</body>

</html>