<?= $this->extend('layout/main_template'); ?>
<?= $this->section('main'); ?>
<div id="kirim-pesan" class="bg-white mt-lg-0 ms-lg-4 mt-3 h-100 text-dark" style="padding-top: 64px;">
    <div class="container-fluid col-lg-12 px-4 py-md-5 py-2 ">
        <?= csrf_field('secureData'); ?>
        <div class="border-bottom text-lg-start text-center mb-3">
            <h1 class="display-4 fw-bold lh-1 mb-3">Data PMKS</h1>
            <?= getFlash('message'); ?>
        </div>
        <div class="row g-5 py-3">
            <table id="table" class="table-responsive table-hover table align-middle table-striped w-100" class="w-100">
            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        // Create configuration
        config = {
            table_id: 'table',
            // Ajax server side config
            ajax: {
                url: baseUrl("data/pmks"),
                type: "GET",
                data: {
                    orderable: ['community_name', 'community_address', 'pmpsks_name', 'community_status'],
                    searchable: ['community_identifier', 'community_name', 'community_address', 'pmpsks_name', 'community_status']
                }
            },
            // Configure buttons
            buttons: {
                add: {
                    url: baseUrl('data/pmks/tambah')
                },
                xlsx: true,
                delete: {
                    url: baseUrl('data/pmks'),
                    postData: postData()
                },
                manipulateSelected: {
                    url: baseUrl('data/pmks'),
                    text: '<i class="bi bi-toggles"></i>',
                    title: 'Ubah Status Disetujui/Belum Disetujui',
                    postData: postData()
                },
                custom: {
                    text: '<i class="bi bi-cloud-upload"></i>',
                    title: 'Tambah dengan spreadsheet',
                    action: function() {
                        window.location.href = baseUrl('data/pmks/tambah-spreadsheet');
                    }
                }
            },
            columns: [{
                    title: "Nama",
                    name: "community_name",
                    data: "community_name",
                    className: 'text-center text-lg-start',
                    render: function(data, type, row) {
                        return `<div class="d-block text-start d-lg-inline fw-semibold fs-6">${row.community_name}<br>
                        <span class='badge small p-0 text-secondary'>${row.community_identifier}</span>
                        </div>`;
                    },

                },
                {
                    title: "Alamat",
                    name: "community_address",
                    data: "community_address",
                    className: 'text-start',
                    render: function(data, type, row) {
                        return `<span class="small text-dark">${data}</span>`;
                    }

                },
                {
                    title: "Tipe",
                    name: "pmpsks_name",
                    data: "pmpsks_name",
                    className: 'text-start',
                    type: 'array',
                    options: <?= $pmks_types; ?>,
                    render: function(data) {
                        return `<span class="small text-dark">${data}</span>`;
                    }

                },
                {
                    title: "Status",
                    name: "community_status",
                    data: "community_status",
                    className: 'text-center',
                    type: 'array',
                    options: [{
                            value: 'Disetujui',
                            text: 'Disetujui'
                        },
                        {
                            value: 'Belum Disetujui',
                            text: 'Belum Disetujui'
                        },

                    ],
                    render: function(data) {
                        if (data == 'Belum Disetujui') {
                            className = 'text-dark bg-warning'
                        } else {
                            className = 'bg-success'
                        }
                        return `<span class="${className} badge">${data}</span>`;
                    }
                },
                {
                    title: "Aksi",
                    name: "unique_id",
                    data: "unique_id",
                    className: "text-center",
                    render: function(data, type, row) {
                        return (
                            `<button data-bs-id="${data}" data-bs-title="Foto ${row.community_name}" data-bs-toggle="modal" title="Lihat Gambar" data-bs-target="#image_modal" class="btn btn-sm btn-outline-secondary m-1 rounded">Foto</button><a href="${baseUrl(`data/pmks/${data}`)}" class="btn m-1 btn-sm btn-primary rounded">Ubah</a>`
                        );
                    },
                },
            ],

        }
        createDatatable(config)
        const imageModal = document.getElementById('image_modal')
        imageModal.addEventListener('show.bs.modal', event => {
            // Button that triggered the modal
            const button = event.relatedTarget;
            // Extract info from data-bs-* attributes
            const modalBody = imageModal.querySelector('.modal-body')
            modalBody.innerHTML = ` <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Memuat...</span>
                </div>`;
            (function() {
                $.ajax({
                    type: "get",
                    url: baseUrl(`data/pmks/gambar`),
                    data: {
                        uuid: button.getAttribute('data-bs-id')
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.length > 0) {
                            var images = ''
                            response.forEach(function(url) {
                                images += `<img class='col-md-3 shadow-sm col-12 m-md-2 mb-2 rounded' src="${url}">`
                            })
                            modalBody.innerHTML = images;
                        } else {
                            modalBody.innerHTML = '<span class="fs-4">Tidak Ada Gambar</span>';
                        }
                    }
                });
            })();
            const title = button.getAttribute('data-bs-title')
            const modalTitle = imageModal.querySelector('.modal-title')
            modalTitle.textContent = title
        })
    })
</script>
<!-- Modal -->
<div class="modal fade" id="image_modal" tabindex="-1" aria-labelledby="image_modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="image_modal">Foto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body d-block text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Memuat...</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/jszip-2.5.0/dt-1.12.1/b-2.2.3/b-html5-2.2.3/b-print-2.2.3/date-1.1.2/sb-1.3.4/sl-1.4.0/datatables.min.css" />

<script type="text/javascript" defer="true" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" defer="true" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" defer="true" src="https://cdn.datatables.net/v/bs5/jszip-2.5.0/dt-1.12.1/b-2.2.3/b-html5-2.2.3/b-print-2.2.3/date-1.1.2/sb-1.3.4/sl-1.4.0/datatables.min.js"></script>
<script type="text/javascript" defer="true" charset="utf8" src="<?= base_url('js/datatables/config.min.js'); ?>"></script>

<?= $this->endSection(); ?>