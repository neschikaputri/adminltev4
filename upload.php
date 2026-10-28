<?php
if(isset($_POST["submit"])) {
// menerima data dari form_upload
//   $namaFile = $_FILES["foto"]["name"];
//   $tipeFile = $_FILES["foto"]["type"];
   $ukuranFile = $_FILES["foto"]["size"];
   $fileSementara = $_FILES["foto"]["tmp_name"];
//   $statusError = $_FILES["foto"]["error"];

//   //tampilan isi datanya
//   echo "Nama File: " . $namaFile . "<br>";
//   echo "Tipe File: " . $tipeFile . "<br>";
   echo "Ukuran File: " . $ukuranFile . "<br>";
   echo "File Sementara: " . $fileSementara . "<br>";
//   echo "Status Error: " . $statusError . "<br>";

//inisialisasi status upload
  $uploadOk = 1;


    // Check if image file is a actual image or fake image
    //memeriksa apakah file yang diunggah  'real image' atau 'fake image'
  $cek = getimagesize($fileSementara);
  if($cek !== false) {
    echo "File adalah gambar ASLI - " . $cek["mime"] . ".";
    $uploadOk = 1;
  } else {
    echo "File adalah BUKAN gambar! <br>";
    $uploadOk = 0;
  }

  //cekpoint 2 Duplikasi
  //memeriksa pakah nama file yang SAMA ada di direkroti tujuan
  // Check if file already exists
  //definisikan direktori target/tujuan
  $target_dir = "galeri/";
  $file_path = $target_dir . basename($_FILES["foto"]["name"]);
  //mengubah ekstensi file menjadi huruf kecil
  $imageFileType = strtolower(pathinfo($file_path,PATHINFO_EXTENSION));
    if (file_exists($file_path)) {
        echo "Maaf, file " . basename($_FILES["foto"]["name"]) . " sudah ada.";
        $uploadOk = 0;
    }

    // Checkpoint 3 (Pembatasan Ukuran File)
    // memeriksa apakah ukuran file yang diunggah TIDAK MELEBIHI ukuran yang telah ditentukan
    //ukuran yg ditentukan (mis. 1 MB)
    if ($ukuranFile > 1000000) {
        echo "Maaf, ukuran file melebihi 1 MB<br>";
        $uploadOk = 0;
    }

    // cekpoint 4 (format file)
    // memeriksa ekstensi file termasuk dalam 'whitelist' yang diizinkan (mis, jpg, png, jpeg, gif)'
    if(
      $imageFileType != "jpg" && $imageFileType != "png" && 
      $imageFileType != "jpeg" && $imageFileType != "gif" 
      ) {
      echo "Maaf, hanya file JPG, PNG, JPEG, dan GIF yang diizinkan.";
      $uploadOk = 0;
    }


    //jika status uploadok = 1 pindahkan file ke direktori tujuan
    if ($uploadok == 1) {
      if (move_uploaded_file($fileSementara, $file_path)) {
        echo "file ". basename($_FILES["foto"]["name"]). " berhasil diunggah.";
      } 

      //salin alamat file (path name) ke tabel: 'galeri di databse:' db5d
      //koneksi ke db
      require_once _DIR_ . "/phpdb/koneksi.php";
      ///jalankan query
      $sql = "insert into galeri (nama_file) values ('$file_path')";
      // cek apakah query berhasil di jalankan
      if (mysqli_query($conn, $sql)) {
        echo "Data baru berhasil ditambahkan";
      } else {
        echo "Kesalahan dalam menambahkan data:" . mysqli_error($conn);
      }
      // tutup koneksi ke db
      //mysqli_close($conn);
      // redirect ke bacagaleri
      header('Location: bacagaleri.php');
    }
}
?>







