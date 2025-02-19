<?php
include 'db_resume_rapat.php'; // Koneksi database

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Pastikan semua variabel POST didefinisikan
    $tanggal = isset($_POST['tanggal']) ? $_POST['tanggal'] : '';
    $pukul = isset($_POST['pukul']) ? $_POST['pukul'] : '';
    $tempat = isset($_POST['tempat']) ? $_POST['tempat'] : '';
    $acara = isset($_POST['acara']) ? $_POST['acara'] : '';
    $kesimpulan = isset($_POST['kesimpulan']) ? $_POST['kesimpulan'] : '';
    $nama = isset($_POST['nama']) ? $_POST['nama'] : '';
    $instansi = isset($_POST['instansi']) ? $_POST['instansi'] : '';
    $paraf_ttd = isset($_POST['paraf_ttd']) ? $_POST['paraf_ttd'] : '';

    // Periksa apakah paraf_ttd ada dan dalam format Base64
    if (!empty($paraf_ttd)) {
        $img = str_replace('data:image/png;base64,', '', $paraf_ttd);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);

        // Simpan sebagai JPG di direktori signatures/
        $file_name = 'signatures/' . time() . '.jpg';
        file_put_contents($file_name, $data);
    } else {
        $file_name = ''; // Jika tidak ada tanda tangan, set sebagai kosong
    }

    // Gunakan prepared statement untuk menghindari SQL Injection
    $stmt = $conn->prepare("INSERT INTO resume_rapat (tanggal, pukul, tempat, acara, kesimpulan, nama, instansi, paraf_ttd) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $tanggal, $pukul, $tempat, $acara, $kesimpulan, $nama, $instansi, $file_name);

    if ($stmt->execute()) {
        echo '<script>alert("Data berhasil disimpan!"); window.location.href="index.php";</script>';
    } else {
        echo '<script>alert("Gagal menyimpan data: ' . $conn->error . '");</script>';
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resume Rapat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function resetForm() {
            document.getElementById("form-resume").reset();
        }
    </script>
</head>
<body class="container mt-4">
    <h2 class="text-center">RESUME RAPAT</h2>
    <form id="form-resume" method="POST">
        <div class="row mb-3">
            <div class="col-md-9">
                <label>Tanggal:</label>
                <input type="date" class="form-control" name="tanggal" required>
            </div>
            <div class="col-md-9">
                <label>Pukul:</label>
                <input type="time" class="form-control" name="pukul" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-9">
                <label>Tempat:</label>
                <input type="text" class="form-control" name="tempat" required>
            </div>
            <div class="col-md-9">
                <label>Acara:</label>
                <input type="text" class="form-control" name="acara" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-9">
                <label>Kesimpulan:</label>
                <textarea class="form-control" name="kesimpulan" required></textarea>
            </div>
        </div>
        <h4>Daftar Hadir</h4>
        <div class="row mb-3">
            <div class="col-md-4">
                <input type="text" class="form-control" name="nama" placeholder="Nama" required>
            </div>
            <div class="col-md-4">
                <input type="text" class="form-control" name="instansi" placeholder="Instansi" required>
            </div>
            <div class="col-md-4">
                <canvas id="signatureCanvas" class="border"></canvas>
                <input type="hidden" name="paraf_ttd" id="paraf_ttd">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <button type="button" class="btn btn-secondary" onclick="resetForm()">Reset</button>
        <a href="preview.php" target="_blank" class="btn btn-success">Preview</a>
    </form>

    <script>
        var canvas = document.getElementById("signatureCanvas");
        var ctx = canvas.getContext("2d");
        var drawing = false;

        canvas.addEventListener("mousedown", function (e) {
            drawing = true;
            ctx.beginPath();
            ctx.moveTo(e.offsetX, e.offsetY);
        });

        canvas.addEventListener("mousemove", function (e) {
            if (!drawing) return;
            ctx.lineTo(e.offsetX, e.offsetY);
            ctx.stroke();
        });

        canvas.addEventListener("mouseup", function () {
            drawing = false;
            document.getElementById("paraf_ttd").value = canvas.toDataURL("image/png");
        });
    </script>
</body>
</html>
