<?= $this->extend('layout/main_template'); ?>
<?= $this->section('main'); ?>
<div id="kirim-pesan" class="bg-white mt-lg-0 ms-lg-4 mt-3 h-100 text-dark" style="padding-top: 64px;">
    <div class="container-fluid col-lg-12 px-4 py-md-5 py-2 ">
        <?= csrf_field('secureData'); ?>
        <div class="border-bottom text-lg-start text-center mb-3">
            <h1 class="display-4 fw-bold lh-1 mb-3">Data Pengurus</h1>
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
                url: baseUrl("konten/profil-karang-taruna/pengurus"),
                type: "GET",
                data: {
                    orderable: ['member_name', 'member_position', 'member_type', 'member_active'],
                    searchable: ['member_name', 'member_position', 'member_type', 'member_active']
                }
            },
            // Configure buttons
            buttons: {
                add: {
                    url: baseUrl('konten/profil-karang-taruna/pengurus/tambah')
                },
                delete: {
                    url: baseUrl('konten/profil-karang-taruna/pengurus'),
                    postData: postData()
                },
            },
            columns: [{
                    title: "Nama",
                    name: "member_name",
                    data: "member_name",
                    className: 'text-center text-lg-start',
                    render: function(data, type, row) {
                        return `<img id="id-${row.member_name}"  loading="lazy" onerror="imageFallbackOption(this,'${row.member_name}')" class="rounded-circle img-fluid me-2" style="object-fit: cover;width:48px; height:48px; object-position: top;"  src="${row.member_image}" alt="Foto Pengurus" />
                        <div class="d-block d-lg-inline fw-semibold fs-6">${row.member_name}</div>`;
                    },

                },
                {
                    title: "Jabatan",
                    name: "member_position",
                    data: "member_position",
                    className: 'text-center',
                    render: function(data, type, row) {
                        return `<span>${data}</span>`;
                    }

                },
                {
                    title: "Tipe",
                    name: "member_type",
                    data: "member_type",
                    className: 'text-center',
                    type: 'array',
                    options: [{
                            value: '1',
                            text: 'Ketua'
                        },
                        {
                            value: '2',
                            text: 'Top Level'
                        },
                        {
                            value: '3',
                            text: 'Kegiatan Khusus'
                        },
                        {
                            value: '4',
                            text: 'Anggota'
                        },

                    ],
                    render: function(data) {
                        switch (data) {
                            case '1':
                                type = 'Ketua'
                                className = 'bg-danger'
                                break;
                            case '2':
                                type = 'Top Level'
                                className = 'bg-warning text-dark'
                                break;
                            case '3':
                                type = 'Kabid'
                                className = 'bg-success'
                                break;
                            default:
                                type = 'Anggota'
                                className = 'bg-light text-dark'
                                break;
                        }
                        return `<span class="badge ${className}">${type}</span>`;
                    }

                },
                {
                    title: "Aktif?",
                    name: "member_active",
                    data: "member_active",
                    className: 'text-center',
                    type: 'array',
                    options: [{
                            value: 'Aktif',
                            text: 'Aktif'
                        },
                        {
                            value: 'Nonaktif',
                            text: 'Nonaktif'
                        },

                    ],
                    render: function(data) {
                        return `<span>${data}</span>`;
                    }
                },
                {
                    title: "Aksi",
                    name: "unique_id",
                    data: "unique_id",
                    className: "text-center",
                    render: function(data, type, row) {
                        return (
                            `<button data-name-target="id-${row.member_name}" data-bs-title="Foto ${row.member_name}" data-bs-toggle="modal" data-bs-target="#image_modal" class="btn btn-sm btn-outline-secondary m-1 rounded" title="Lihat Gambar">Foto</button><a href="${baseUrl(`konten/profil-karang-taruna/pengurus/${data}`)}" class="btn m-1 btn-sm btn-primary rounded">Ubah</a>`
                        );
                    },
                },
            ],

        }
        createDatatable(config)
        const imageModal = document.getElementById('image_modal')
        imageModal.addEventListener('show.bs.modal', event => {
            // Button that triggered the modal
            const button = event.relatedTarget
            // Extract info from data-bs-* attributes
            const img = document.getElementById(button.getAttribute('data-name-target'))

            const src = img.getAttribute('src')
            const title = button.getAttribute('data-bs-title')
            // If necessary, you could initiate an AJAX request here
            // and then do the updating in a callback.
            //
            // Update the modal's content.
            const modalTitle = imageModal.querySelector('.modal-title')
            const modalBodyInput = imageModal.querySelector('.modal-body img')

            modalTitle.textContent = title
            modalBodyInput.setAttribute('src', src)
        })
    })
    var imageFallbackOption = function(el, name) {
        el.setAttribute("src", `https://avatars.dicebear.com/api/initials/${encodeURIComponent(name)}.svg?background=%234F4F4F&fontSize=35&bold=true`);
    }
</script>
<!-- Modal -->
<div class="modal fade" id="image_modal" tabindex="-1" aria-labelledby="image_modal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="image_modal">Foto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <img class='w-100 rounded' src="">
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