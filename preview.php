<?php
// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_resume_rapat";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil data terakhir dari database
$sql = "SELECT * FROM resume_rapat ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);
$resume = $result->fetch_assoc();

// Ambil daftar hadir
$sql = "SELECT * FROM daftar_hadir WHERE resume_id = " . $resume['id'];
$hadir_result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Resume Rapat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
    <h2 class="text-center">RESUME RAPAT</h2>
    <div class="row">
        <div class="col-lg-9 col-md-12">
            <table class="table table-bordered">
                <tr>
                    <th>Tanggal</th>
                    <td><?= $resume['tanggal'] ?></td>
                </tr>
                <tr>
                    <th>Pukul</th>
                    <td><?= $resume['pukul'] ?></td>
                </tr>
                <tr>
                    <th>Tempat</th>
                    <td><?= $resume['tempat'] ?></td>
                </tr>
                <tr>
                    <th>Acara</th>
                    <td><?= $resume['acara'] ?></td>
                </tr>
                <tr>
                    <th>Kesimpulan</th>
                    <td><?= nl2br($resume['kesimpulan']) ?></td>
                </tr>
            </table>
        </div>
    </div>

    <h4>Daftar Hadir</h4>
    <div class="row">
        <div class="col-lg-8 col-md-12">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Instansi</th>
                        <th>Paraf/TTD</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while ($row = $hadir_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $row['nama'] ?></td>
                        <td><?= $row['instansi'] ?></td>
                        <td><img src="<?= $row['paraf_ttd'] ?>" width="150"></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <a href="index.php" class="btn btn-primary">Kembali</a>
    <a href="edit.php" class="btn btn-danger">Perbaiki</a>
</body>
</html>

<?php $conn->close(); ?>
