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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tanggal = $_POST['tanggal'];
    $pukul = $_POST['pukul'];
    $tempat = $_POST['tempat'];
    $acara = $_POST['acara'];
    $kesimpulan = $_POST['kesimpulan'];
    
    $sql = "UPDATE resume_rapat SET tanggal=?, pukul=?, tempat=?, acara=?, kesimpulan=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $tanggal, $pukul, $tempat, $acara, $kesimpulan, $resume['id']);
    $stmt->execute();
    
    echo "<script>alert('Data berhasil diperbarui!'); window.location.href='preview.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Resume Rapat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
    <h2 class="text-center">EDIT RESUME RAPAT</h2>
    <form method="POST">
        <div class="row mb-3">
            <div class="col-md-6">
                <label>Tanggal:</label>
                <input type="date" class="form-control" name="tanggal" value="<?= $resume['tanggal'] ?>" required>
            </div>
            <div class="col-md-6">
                <label>Pukul:</label>
                <input type="time" class="form-control" name="pukul" value="<?= $resume['pukul'] ?>" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label>Tempat:</label>
                <input type="text" class="form-control" name="tempat" value="<?= $resume['tempat'] ?>" required>
            </div>
            <div class="col-md-6">
                <label>Acara:</label>
                <input type="text" class="form-control" name="acara" value="<?= $resume['acara'] ?>" required>
            </div>
        </div>
        <div class="mb-3">
            <label>Kesimpulan:</label>
            <textarea class="form-control" name="kesimpulan" required><?= $resume['kesimpulan'] ?></textarea>
        </div>
        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
        <a href="preview.php" class="btn btn-primary">Kembali</a>
    </form>
</body>
</html>

<?php $conn->close(); ?>
