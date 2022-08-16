<?= $this->extend('layout/main_template'); ?>
<?= $this->section('main'); ?>
<div id="kirim-pesan" class="bg-white mt-lg-0 ms-lg-4 mt-3 h-100 text-dark" style="padding-top: 64px;">
    <div class="container-fluid col-lg-12 px-4 py-md-5 py-2 ">
        <?= csrf_field('secureData'); ?>
        <div class="border-bottom text-lg-start text-center mb-3">
            <h1 class="display-4 fw-bold lh-1 mb-3">Data Pesan</h1>
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
                url: baseUrl("data/pesan"),
                type: "GET",
                data: {
                    orderable: ['message_sender', 'message_type', 'message_whatsapp', 'message_status', 'created_at'],
                    searchable: ['message_sender', 'message_type', 'message_whatsapp', 'message_status', 'created_at']
                }
            },
            // Configure buttons
            buttons: {
                delete: {
                    url: baseUrl('data/pesan'),
                    postData: postData()
                },
                manipulateSelected: {
                    url: baseUrl('data/pesan'),
                    text: '<i class="bi bi-bookmark-check"></i>',
                    title: 'Ubah Status Dibaca/Belum Dibaca',
                    postData: postData()
                },
            },
            columns: [{
                    title: "Nama Pengirim",
                    name: "message_sender",
                    data: "message_sender",
                    className: 'text-center text-lg-start',
                    render: function(data) {
                        return `<div class="d-block text-start d-lg-inline fw-semibold fs-6">${data}</div>`;
                    },

                },
                {
                    title: "Tipe",
                    name: "message_type",
                    data: "message_type",
                    className: 'text-center',
                    type: 'array',
                    options: [{
                            value: 'Kritik & Saran',
                            text: 'Kritik & Saran'
                        },
                        {
                            value: 'Laporan/Aduan',
                            text: 'Laporan/Aduan'
                        },

                    ],
                    render: function(data) {
                        if (data == 'Laporan/Aduan') {
                            className = 'bg-danger text-light'
                        } else {
                            className = 'bg-warning text-dark'
                        }
                        return `<span class="small badge ${className}">${data}</span>`;
                    }

                },
                {
                    title: "No Whatsapp",
                    name: "message_whatsapp",
                    data: "message_whatsapp",
                    className: 'text-center',
                    render: function(data) {
                        return `<a class="fs-3" target="_blank" href="https://wa.me/${data}"><i class="bi text-success bi-whatsapp"></i></a>`;
                    }

                },
                {
                    title: "Status",
                    name: "message_status",
                    data: "message_status",
                    className: 'text-center',
                    type: 'array',
                    options: [{
                            value: 'Dibaca',
                            text: 'Dibaca'
                        },
                        {
                            value: 'Belum Dibaca',
                            text: 'Belum Dibaca'
                        },

                    ],
                    render: function(data) {
                        if (data == 'Belum Dibaca') {
                            className = 'text-dark bg-warning'
                        } else {
                            className = 'text-light bg-secondary'
                        }
                        return `<span class="${className} badge">${data}</span>`;
                    }
                },
                {
                    title: "Timestamp",
                    name: "created_at",
                    data: "created_at",
                    className: "text-center",
                    type: 'date',
                    render: function(data, type, row) {
                        return (
                            `<span class="small">${data}</span>`
                        );
                    },
                },
                {
                    title: "Aksi",
                    name: "unique_id",
                    data: "unique_id",
                    className: "text-center",
                    render: function(data, type, row) {
                        return (
                            `<button data-message="${row.message_text}" data-bs-title="Pesan dari ${row.message_sender}" data-bs-toggle="modal" title="Lihat Pesan" data-bs-target="#message_modal" class="btn btn-sm btn-outline-secondary m-1 rounded">Lihat Pesan</button>`
                        );
                    },
                },
            ],

        }
        createDatatable(config)
        const messageModal = document.getElementById('message_modal')
        messageModal.addEventListener('show.bs.modal', event => {
            // Button that triggered the modal
            const button = event.relatedTarget;
            const title = button.getAttribute('data-bs-title')
            const message = button.getAttribute('data-message')
            const modalTitle = messageModal.querySelector('.modal-title')
            const modalBody = messageModal.querySelector('.modal-body')
            modalTitle.textContent = title
            modalBody.textContent = message
        })
    })
</script>
<!-- Modal -->
<div class="modal fade" id="message_modal" tabindex="-1" aria-labelledby="message_modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="message_modal">Pesan dari</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

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