<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db5d";

// Membuat koneksi
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Cek koneksi
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "SELECT * FROM nilai_mhsw";
// Eksekusi sql
$result = mysqli_query($conn, $sql);
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

    <!-- AdminLTE & Bootstrap CSS CDN -->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/css/adminlte.min.css"
      crossorigin="anonymous"
    />
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
                      <div class="d-flex flex-wrap justify-content-md-end gap-2">
                        <div class="input-group input-group-sm w-auto">
                          <span class="input-group-text">
                            <i class="bi bi-search" aria-hidden="true"></i>
                          </span>
                          <input
                            type="search"
                            id="user-search"
                            class="form-control"
                            placeholder="Cari berdasarkan nama"
                            aria-label="Cari berdasarkan nama"
                            style="width: 200px"
                          />
                        </div>
                        <select
                          id="user-role-filter"
                          class="form-select form-select-sm w-auto"
                          aria-label="Filter by predikat"
                        >
                          <option value="all" selected>Semua Predikat</option>
                          <option value="sangat-memuaskan">Sangat Memuaskan</option>
                          <option value="memuaskan">Memuaskan</option>
                          <option value="cukup">Cukup</option>
                          <option value="kurang">Kurang</option>
                          <option value="tidak-lulus">Tidak Lulus</option>
                        </select>
                        <a href="form_nilai.html" class="btn btn-sm btn-primary">
                          <i class="bi bi-person-plus-fill me-1" aria-hidden="true"></i>
                          Tambah Data Nilai
                        </a>
                      </div>
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
                          <th>NIM</th>
                          <th>Nama Lengkap</th>
                          <th>Nilai Akhir</th>
                          <th>Nilai Huruf</th>
                          <th>Predikat</th>
                          <th>Tgl Dibuat</th>
                          <th class="text-end">Aksi</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        while($row = mysqli_fetch_assoc($result)) {
                        ?>
                        <tr>
                          <td><?php echo $row["nim"]; ?></td>
                          <td>
                            <div class="d-flex align-items-center">
                              <img
                                src="https://adminlte.io/themes/v3/dist/img/user1-128x128.jpg"
                                alt=""
                                class="img-size-32 rounded-circle me-2"
                                style="width: 32px; height: 32px; object-fit: cover;"
                              />
                              <span class="fw-medium">
                                <?php echo $row["nama_lengkap"]; ?>
                              </span>
                            </div>
                          </td>
                          <td><?php echo $row["nilai_akhir"]; ?></td>
                          <td><?php echo $row["nilai_huruf"]; ?></td>
                          <td><?php echo $row["predikat"]; ?></td>
                          <td><?php echo $row["entry_date"]; ?></td>
                          <td class="text-end">
                            <div class="btn-group btn-group-sm">
                              <a href="form_editnilai.php?id=<?php echo $row['nim']; ?>" class="btn btn-outline-secondary" aria-label="Edit">
                                <i class="bi bi-pencil" aria-hidden="true"></i>
                              </a>
                              <a href="hapus_nilai.php?id=<?php echo $row['nim']; ?>" onclick="return confirm('Apakah anda yakin ingin menghapus data ini');" class="btn btn-outline-danger" aria-label="Delete">
                                <i class="bi bi-trash" aria-hidden="true"></i>
                              </a>
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
                    mysqli_close($conn);
                    ?>
                  </div>
                </div>
                <!--end::Card Body-->

                <!--begin::Card Footer-->
                <div class="card-footer clearfix">
                  <div class="float-start pt-1 fs-7 text-body-secondary">
                    Menampilkan data dari database
                  </div>
                  <ul class="pagination pagination-sm m-0 float-end">
                    <li class="page-item disabled"><a class="page-link" href="#">&laquo;</a></li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item"><a class="page-link" href="#">4</a></li>
                    <li class="page-item"><a class="page-link" href="#">5</a></li>
                    <li class="page-item"><a class="page-link" href="#">&raquo;</a></li>
                  </ul>
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

    <!-- Bootstrap & AdminLTE JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/js/adminlte.min.js" crossorigin="anonymous"></script>
  </body>
</html>