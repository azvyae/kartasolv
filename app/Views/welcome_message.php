<form action="" method="post">
    <?php $data = [
        [
            'mission' => 'Abcdev ele reavxix',
            'desc' => 'Devera reavxix'
        ],
        [
            'mission' => 'Abcdev ele reavxix',
            'desc' => 'Devera reavxix'
        ]
    ];
    $text = '';
    foreach ($data as $d) {
        $text .= "-{$d['mission']} ({$d['desc']})\n";
    }
    ?>

    <textarea name="lala" id="" cols="30" rows="10"><?= $text; ?></textarea>
    <button type="submit">Kirim</button>
</form>
<script>
    $(function() {
        $(".address").select2({
            language: 'id',
            placeholder: 'Kecamatan, Kabupaten/Kota, Provinsi',
        })
    });
</script>
<script src="<?= base_url('select2/select2.full.min.js'); ?>"></script>
<script src="<?= base_url('select2/i18n/id.js'); ?>"></script>