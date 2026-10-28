<?php
// Membuat koneksi (pakai koneksi.php)
include "koneksi.php";

$kandidat = [
    "admin-lte-v4.10.0/dist",
    "admin-lte-v4.10.0",
];

$al_css = "https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/css/adminlte.min.css";
$al_js  = "https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/js/adminlte.min.js";
foreach ($kandidat as $k) {
    if (file_exists(__DIR__ . "/$k/css/adminlte.min.css") && file_exists(__DIR__ . "/$k/js/adminlte.min.js")) {
        $al_css = "$k/css/adminlte.min.css";
        $al_js  = "$k/js/adminlte.min.js";
        break;
    }
}

// ========== AMBIL PARAMETER DARI URL ==========
// 1. Searching (WHERE): nama + predikat
$q        = isset($_GET['q']) ? trim($_GET['q']) : '';
$predikat = isset($_GET['predikat']) ? trim($_GET['predikat']) : 'all';

// 2. Sorting (ORDER BY nilai_akhir)
$sort = isset($_GET['sort']) ? strtolower($_GET['sort']) : '';
if ($sort !== 'asc' && $sort !== 'desc') {
    $sort = '';
}

// 3. Paging (LIMIT)
$allowed_per_page = [5, 7, 10, 15];
$per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 7;
if (!in_array($per_page, $allowed_per_page)) {
    $per_page = 7;
}
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

$daftar_predikat = ['Sangat Memuaskan', 'Memuaskan', 'Cukup Memuaskan', 'Kurang Memuaskan', 'Tidak Lulus'];

// ========== SUSUN KONDISI WHERE ==========
$where  = [];
$types  = '';
$params = [];

if ($q !== '') {
    $where[]  = "nama_lengkap LIKE ?";
    $types   .= 's';
    $params[] = "%" . $q . "%";
}
if ($predikat !== 'all' && in_array($predikat, $daftar_predikat)) {
    $where[]  = "predikat = ?";
    $types   .= 's';
    $params[] = $predikat;
}
$where_sql = count($where) > 0 ? " WHERE " . implode(" AND ", $where) : "";

// ========== HITUNG TOTAL DATA ==========
$stmt_total = $conn->prepare("SELECT COUNT(*) FROM nilai_mhsw" . $where_sql);
if ($types !== '') {
    $stmt_total->bind_param($types, ...$params);
}
$stmt_total->execute();
$stmt_total->bind_result($total);
$stmt_total->fetch();
$stmt_total->close();

$total_pages = max(1, (int)ceil($total / $per_page));
if ($page > $total_pages) {
    $page = $total_pages;
}
$offset = ($page - 1) * $per_page;

// ========== QUERY UTAMA: WHERE + ORDER BY + LIMIT ==========
$order_sql = " ORDER BY nilai_akhir " . ($sort === 'asc' ? 'ASC' : 'DESC');
$sql  = "SELECT * FROM nilai_mhsw" . $where_sql . $order_sql . " LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param($types . 'ii', ...array_merge($params, [$per_page, $offset]));
$stmt->execute();
// Eksekusi sql
$result = $stmt->get_result();

// ========== FUNGSI BANTU ==========
// Membuat link dengan mempertahankan search, sort, dan paging
function buat_url($override = []) {
    $base = [
        'q'        => $_GET['q'] ?? '',
        'predikat' => $_GET['predikat'] ?? 'all',
        'sort'     => $_GET['sort'] ?? '',
        'per_page' => $_GET['per_page'] ?? '',
        'page'     => $_GET['page'] ?? '',
    ];
    $merged = array_filter(array_merge($base, $override), function ($v) {
        return $v !== '' && $v !== null;
    });
    return '?' . http_build_query($merged);
}

$next_sort = ($sort === 'asc') ? 'desc' : 'asc';
$sort_icon = 'bi-arrow-down-up';
if ($sort === 'asc')  $sort_icon = 'bi-sort-numeric-down';
if ($sort === 'desc') $sort_icon = 'bi-sort-numeric-up-alt';

$mulai  = $total > 0 ? $offset + 1 : 0;
$sampai = min($offset + $per_page, $total);
?>
<!doctype html>
<html lang="en" data-bs-theme="dark">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <title>AdminLTE 4 | Rekap Nilai Mahasiswa</title>

    <!-- Google Fonts -->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
      crossorigin="anonymous"
    />

    <!-- Bootstrap Icons CDN -->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
      crossorigin="anonymous"
    />

    <!-- AdminLTE v4 (file lokal, otomatis cadangan CDN) -->
    <link rel="stylesheet" href="<?php echo $al_css; ?>" />
  </head>

  <body class="bg-body-tertiary p-3">

    <!--begin::App Main-->
    <main class="app-main m-0 p-0">

      <!--begin::App Content Header-->
      <div class="app-content-header mb-3">
        <div class="container-fluid">
          <div class="row">
            <div class="col-sm-6">
              <h1 class="mb-0 fs-3">Rekap Nilai Mahasiswa</h1>
            </div>
            <div class="col-sm-6">
              <nav aria-label="breadcrumb">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><a href="#">Beranda</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Nilai</li>
                </ol>
              </nav>
            </div>
          </div>
        </div>
      </div>
      <!--end::App Content Header-->

      <!--begin::App Content-->
      <div class="app-content">
        <div class="container-fluid">

          <?php if (isset($_GET['msg'])) {
              $tipe = (($_GET['tipe'] ?? '') === 'danger') ? 'danger' : 'success'; ?>
            <div class="alert alert-<?php echo $tipe; ?> alert-dismissible fade show" role="alert">
              <?php echo htmlspecialchars($_GET['msg']); ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php } ?>

          <div class="row">
            <div class="col-12">

              <!--begin::Card Direktori Nilai-->
              <div class="card mb-4">

                <!--begin::Card Header-->
                <div class="card-header">
                  <div class="row g-2 align-items-center">
                    <div class="col-12 col-md-4">
                      <h3 class="card-title">Direktori Nilai</h3>
                    </div>
                    <div class="col-12 col-md-8">
                      <form method="get" class="d-flex flex-wrap justify-content-md-end gap-2">
                        <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>">
                        <input type="hidden" name="per_page" value="<?php echo $per_page; ?>">

                        <div class="input-group input-group-sm w-auto">
                          <span class="input-group-text">
                            <i class="bi bi-search" aria-hidden="true"></i>
                          </span>
                          <input
                            type="search"
                            name="q"
                            id="user-search"
                            class="form-control"
                            placeholder="Cari berdasarkan nama"
                            aria-label="Cari berdasarkan nama"
                            value="<?php echo htmlspecialchars($q); ?>"
                            style="width: 200px"
                          />
                        </div>
                        <select
                          name="predikat"
                          id="user-role-filter"
                          class="form-select form-select-sm w-auto"
                          aria-label="Filter by predikat"
                          onchange="this.form.submit()"
                        >
                          <option value="all" <?php echo $predikat === 'all' ? 'selected' : ''; ?>>Semua Predikat</option>
                          <?php foreach ($daftar_predikat as $p) { ?>
                            <option value="<?php echo $p; ?>" <?php echo $predikat === $p ? 'selected' : ''; ?>><?php echo $p; ?></option>
                          <?php } ?>
                        </select>
                        <button type="submit" class="btn btn-sm btn-outline-secondary">Cari</button>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                          <i class="bi bi-person-plus-fill me-1" aria-hidden="true"></i>
                          Tambah Data Nilai
                        </button>
                      </form>
                    </div>
                  </div>
                </div>
                <!--end::Card Header-->

                <!--begin::Card Body-->
                <div class="card-body p-0">
                  <div class="table-responsive">
                    <?php
                    // memproses sql
                    if (mysqli_num_rows($result) > 0) {
                    ?>
                    <table class="table table-hover align-middle m-0">
                      <thead>
                        <tr>
                          <th class="text-nowrap">NIM</th>
                          <th class="text-nowrap">Nama Lengkap</th>
                          <th class="text-nowrap">
                            <a href="<?php echo buat_url(['sort' => $next_sort, 'page' => 1]); ?>"
                               class="text-decoration-none text-reset" title="Urutkan nilai akhir">
                              Nilai Akhir <i class="bi <?php echo $sort_icon; ?> ms-1"></i>
                            </a>
                          </th>
                          <th class="text-nowrap">Nilai Huruf</th>
                          <th class="text-nowrap">Predikat</th>
                          <th class="text-nowrap">Tgl Dibuat</th>
                          <th class="text-end text-nowrap">Aksi</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        while($row = mysqli_fetch_assoc($result)) {
                            // foto: pakai kolom 'foto' kalau ada, kalau tidak pakai foto default
                            $foto = !empty($row['foto'])
                                ? 'uploads/' . htmlspecialchars($row['foto'])
                                : 'https://adminlte.io/themes/v3/dist/img/user1-128x128.jpg';
                        ?>
                        <tr>
                          <td><?php echo htmlspecialchars($row["nim"]); ?></td>
                          <td>
                            <div class="d-flex align-items-center">
                              <img
                                src="<?php echo $foto; ?>"
                                alt=""
                                class="img-size-32 rounded-circle me-2"
                                style="width: 32px; height: 32px; object-fit: cover;"
                              />
                              <span class="fw-medium">
                                <?php echo htmlspecialchars($row["nama_lengkap"]); ?>
                              </span>
                            </div>
                          </td>
                          <td><?php echo $row["nilai_akhir"]; ?></td>
                          <td><?php echo htmlspecialchars($row["nilai_huruf"]); ?></td>
                          <td><?php echo htmlspecialchars($row["predikat"]); ?></td>
                          <td><?php echo $row["created_at"]; ?></td>
                          <td class="text-end">
                            <div class="btn-group btn-group-sm">
                              <button type="button" class="btn btn-outline-secondary btn-edit" title="Edit" aria-label="Edit"
                                      data-nim="<?php echo htmlspecialchars($row['nim']); ?>"
                                      data-nama="<?php echo htmlspecialchars($row['nama_lengkap']); ?>"
                                      data-nilai="<?php echo number_format($row['nilai_akhir'], 2, '.', ''); ?>">
                                <i class="bi bi-pencil" aria-hidden="true"></i>
                              </button>
                              <button type="button" class="btn btn-outline-danger btn-hapus" title="Hapus" aria-label="Delete"
                                      data-nim="<?php echo htmlspecialchars($row['nim']); ?>">
                                <i class="bi bi-trash" aria-hidden="true"></i>
                              </button>
                            </div>
                          </td>
                        </tr>
                        <?php
                        }
                        ?>
                      </tbody>
                    </table>
                    <?php
                    } else {
                        echo "<div class='p-4 text-center'>0 results - Data not found!</div>";
                    }
                    ?>
                  </div>
                </div>
                <!--end::Card Body-->

                <!--begin::Card Footer-->
                <div class="card-footer">
                  <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div class="pt-1 fs-7 text-body-secondary">
                      Menampilkan <?php echo $mulai; ?> sampai <?php echo $sampai; ?> dari <?php echo $total; ?> nilai
                    </div>

                    <div class="d-flex align-items-center gap-2">
                      <form method="get" class="d-flex align-items-center gap-2 m-0">
                        <input type="hidden" name="q" value="<?php echo htmlspecialchars($q); ?>">
                        <input type="hidden" name="predikat" value="<?php echo htmlspecialchars($predikat); ?>">
                        <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>">
                        <select name="per_page" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                          <?php foreach ($allowed_per_page as $n) { ?>
                            <option value="<?php echo $n; ?>" <?php echo $per_page == $n ? 'selected' : ''; ?>><?php echo $n; ?></option>
                          <?php } ?>
                        </select>
                        <span class="fs-7 text-body-secondary">baris</span>
                      </form>

                      <ul class="pagination pagination-sm m-0">
                        <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                          <a class="page-link" href="<?php echo buat_url(['page' => $page - 1]); ?>">&laquo;</a>
                        </li>
                        <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                          <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                            <a class="page-link" href="<?php echo buat_url(['page' => $i]); ?>"><?php echo $i; ?></a>
                          </li>
                        <?php } ?>
                        <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                          <a class="page-link" href="<?php echo buat_url(['page' => $page + 1]); ?>">&raquo;</a>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
                <!--end::Card Footer-->

              </div>
              <!--end::Card-->

            </div>
          </div>
        </div>
      </div>
      <!--end::App Content-->

      <!-- Footer Tambahan Sesuai Proyektor -->
      <div class="container-fluid px-3 text-body-secondary fs-7 pb-3 d-flex justify-content-between">
        <span>Hak cipta &copy; pada Prodi TI - 2026. Seluruh hak cipta dilindungi.</span>
        <span>Pemrograman Web - Semester 5 - Desain menggunakan AdminLTE v4</span>
      </div>

    </main>
    <!--end::App Main-->

    <!-- ========== MODAL TAMBAH DATA NILAI ========== -->
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
      <div class="modal-dialog">
        <form action="proses_nilai.php" method="POST" class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalTambahLabel">Tambah Data Nilai</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">NIM</label>
              <input type="text" name="nim" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Nama Lengkap</label>
              <input type="text" name="nama_lengkap" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Nilai Akhir</label>
              <input type="number" name="nilai_akhir" class="form-control" min="0" max="100" step="0.01" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>

    <!-- ========== MODAL EDIT DATA NILAI ========== -->
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
      <div class="modal-dialog">
        <form action="update_nilai.php" method="POST" class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalEditLabel">Edit Data Nilai</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">NIM</label>
              <input type="text" name="nim" id="edit-nim" class="form-control" readonly>
            </div>
            <div class="mb-3">
              <label class="form-label">Nama Lengkap</label>
              <input type="text" name="nama_lengkap" id="edit-nama" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Nilai Akhir</label>
              <input type="number" name="nilai_akhir" id="edit-nilai" class="form-control" min="0" max="100" step="0.01" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Update</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Form tersembunyi untuk hapus (POST ke hapus_nilai.php) -->
    <form id="form-hapus" action="hapus_nilai.php" method="POST" class="d-none">
      <input type="hidden" name="nim" id="hapus-nim">
    </form>

    <!-- Bootstrap JS (CDN) + AdminLTE v4.10.0 JS (file lokal) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="<?php echo $al_js; ?>"></script>
    <script>
      // Tombol pensil: isi form edit lalu buka popup
      document.querySelectorAll('.btn-edit').forEach(function (btn) {
        btn.addEventListener('click', function () {
          document.getElementById('edit-nim').value   = this.dataset.nim;
          document.getElementById('edit-nama').value  = this.dataset.nama;
          document.getElementById('edit-nilai').value = this.dataset.nilai;
          new bootstrap.Modal(document.getElementById('modalEdit')).show();
        });
      });

      // Tombol hapus: konfirmasi lalu kirim POST
      document.querySelectorAll('.btn-hapus').forEach(function (btn) {
        btn.addEventListener('click', function () {
          if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
            document.getElementById('hapus-nim').value = this.dataset.nim;
            document.getElementById('form-hapus').submit();
          }
        });
      });
    </script>
  </body>
</html>
<?php
$stmt->close();
$conn->close();
?>