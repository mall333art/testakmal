<?php
// === Inisialisasi sesi untuk simpan data ===
session_start();
if (!isset($_SESSION['data'])) {
    $_SESSION['data'] = [];
}
$data = &$_SESSION['data'];

// === Tambah Data Baru ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_biodata'])) {
    $new = [
        "id" => uniqid(),
        "nama" => $_POST['nama'],
        "nim" => $_POST['nim'],
        "prodi" => $_POST['prodi'],
        "jk" => $_POST['jk'],
        "hobi" => isset($_POST['hobi']) ? implode(", ", $_POST['hobi']) : "-",
        "alamat" => $_POST['alamat']
    ];
    $data[] = $new;
}

// === Update Data ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_biodata'])) {
    foreach ($data as &$row) {
        if ($row['id'] === $_POST['id']) {
            $row['nama'] = $_POST['nama'];
            $row['nim'] = $_POST['nim'];
            $row['prodi'] = $_POST['prodi'];
            $row['jk'] = $_POST['jk'];
            $row['hobi'] = isset($_POST['hobi']) ? implode(", ", $_POST['hobi']) : "-";
            $row['alamat'] = $_POST['alamat'];
        }
    }
    unset($row);
}

// === Delete Data ===
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $_SESSION['data'] = array_filter($_SESSION['data'], fn($d) => $d['id'] !== $id);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

// === Cari Data ===
$keyword = isset($_GET['cari']) ? $_GET['cari'] : '';
$filtered = $keyword 
    ? array_filter($data, fn($d) => stripos($d['nama'], $keyword)!==false || stripos($d['nim'], $keyword)!==false || stripos($d['prodi'], $keyword)!==false) 
    : $data;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Aplikasi Biodata Mahasiswa</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #e0f7fa; }
    .btn-info {
        background-color: #00bcd4;
        border-color: #00acc1;
    }
    .btn-info:hover {
        background-color: #0097a7;
        border-color: #00838f;
    }
  </style>
</head>
<body class="container py-4">

  <h2 class="text-center mb-4">📋 Aplikasi Biodata Mahasiswa</h2>

  <!-- Form Biodata -->
  <div class="card mb-4 shadow">
    <div class="card-header bg-info text-white">Form Biodata Mahasiswa</div>
    <div class="card-body">
      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Nama Lengkap</label>
          <input type="text" name="nama" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">NIM</label>
          <input type="text" name="nim" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Program Studi</label>
          <select name="prodi" class="form-select" required>
            <option value="Informatika">Informatika</option>
            <option value="Sistem Informasi">Sistem Informasi</option>
            <option value="Teknik Elektro">Teknik Elektro</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Jenis Kelamin</label><br>
          <input type="radio" name="jk" value="Laki-laki" required> Laki-laki
          <input type="radio" name="jk" value="Perempuan"> Perempuan
        </div>
        <div class="mb-3">
          <label class="form-label">Hobi</label><br>
          <input type="checkbox" name="hobi[]" value="Membaca"> Membaca
          <input type="checkbox" name="hobi[]" value="Olahraga"> Olahraga
          <input type="checkbox" name="hobi[]" value="Musik"> Musik
          <input type="checkbox" name="hobi[]" value="Gaming"> Gaming
        </div>
        <div class="mb-3">
          <label class="form-label">Alamat</label>
          <textarea name="alamat" class="form-control" rows="2"></textarea>
        </div>
        <button type="submit" name="submit_biodata" class="btn btn-info text-white">Simpan</button>
      </form>
    </div>
  </div>

  <!-- Form Pencarian -->
  <div class="card mb-4 shadow">
    <div class="card-header bg-info text-white">Cari Data</div>
    <div class="card-body">
      <form method="GET" class="d-flex">
        <input type="text" name="cari" class="form-control me-2" placeholder="Masukkan kata kunci..." value="<?= htmlspecialchars($keyword) ?>">
        <button class="btn btn-info text-white" type="submit">Cari</button>
      </form>
      <?php if ($keyword): ?>
        <p class="mt-2">Anda mencari data dengan kata kunci: <b><?= htmlspecialchars($keyword) ?></b></p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Tabel Data -->
  <div class="card shadow">
    <div class="card-header bg-info text-white">Data Mahasiswa</div>
    <div class="card-body table-responsive">
      <table class="table table-bordered table-striped align-middle">
        <thead>
          <tr>
            <th>Nama</th><th>NIM</th><th>Prodi</th><th>JK</th><th>Hobi</th><th>Alamat</th><th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($filtered): foreach ($filtered as $d): ?>
          <tr>
            <td><?= htmlspecialchars($d['nama']) ?></td>
            <td><?= htmlspecialchars($d['nim']) ?></td>
            <td><?= htmlspecialchars($d['prodi']) ?></td>
            <td><?= htmlspecialchars($d['jk']) ?></td>
            <td><?= htmlspecialchars($d['hobi']) ?></td>
            <td><?= htmlspecialchars($d['alamat']) ?></td>
            <td>
              <!-- Edit -->
              <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#edit<?= $d['id'] ?>">Edit</button>
              <!-- Delete -->
              <a href="?delete=<?= $d['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus data ini?')">Hapus</a>
            </td>
          </tr>

          <!-- Modal Edit -->
          <div class="modal fade" id="edit<?= $d['id'] ?>" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header bg-info text-white">
                  <h5 class="modal-title">Edit Biodata</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <form method="POST">
                    <input type="hidden" name="id" value="<?= $d['id'] ?>">
                    <div class="mb-3">
                      <label>Nama Lengkap</label>
                      <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($d['nama']) ?>" required>
                    </div>
                    <div class="mb-3">
                      <label>NIM</label>
                      <input type="text" name="nim" class="form-control" value="<?= htmlspecialchars($d['nim']) ?>" required>
                    </div>
                    <div class="mb-3">
                      <label>Program Studi</label>
                      <select name="prodi" class="form-select">
                        <option <?= $d['prodi']=='Informatika'?'selected':'' ?>>Informatika</option>
                        <option <?= $d['prodi']=='Sistem Informasi'?'selected':'' ?>>Sistem Informasi</option>
                        <option <?= $d['prodi']=='Teknik Elektro'?'selected':'' ?>>Teknik Elektro</option>
                      </select>
                    </div>
                    <div class="mb-3">
                      <label>Jenis Kelamin</label><br>
                      <input type="radio" name="jk" value="Laki-laki" <?= $d['jk']=='Laki-laki'?'checked':'' ?>> Laki-laki
                      <input type="radio" name="jk" value="Perempuan" <?= $d['jk']=='Perempuan'?'checked':'' ?>> Perempuan
                    </div>
                    <div class="mb-3">
                      <label>Hobi</label><br>
                      <?php $hobiList = explode(", ", $d['hobi']); ?>
                      <input type="checkbox" name="hobi[]" value="Membaca" <?= in_array("Membaca",$hobiList)?'checked':'' ?>> Membaca
                      <input type="checkbox" name="hobi[]" value="Olahraga" <?= in_array("Olahraga",$hobiList)?'checked':'' ?>> Olahraga
                      <input type="checkbox" name="hobi[]" value="Musik" <?= in_array("Musik",$hobiList)?'checked':'' ?>> Musik
                      <input type="checkbox" name="hobi[]" value="Gaming" <?= in_array("Gaming",$hobiList)?'checked':'' ?>> Gaming
                    </div>
                    <div class="mb-3">
                      <label>Alamat</label>
                      <textarea name="alamat" class="form-control"><?= htmlspecialchars($d['alamat']) ?></textarea>
                    </div>
                    <button type="submit" name="update_biodata" class="btn btn-info text-white">Update</button>
                  </form>
                </div>
              </div>
            </div>
          </div>

          <?php endforeach; else: ?>
          <tr><td colspan="7" class="text-center">Belum ada data</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
