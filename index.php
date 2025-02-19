<?php
// Koneksi ke database
$servername = "localhost";
$username = "root"; // Sesuaikan dengan username database Anda
$password = ""; // Sesuaikan dengan password database Anda
$dbname = "db_resume_rapat";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tanggal = $_POST['tanggal'];
    $pukul = $_POST['pukul'];
    $tempat = $_POST['tempat'];
    $acara = $_POST['acara'];
    $kesimpulan = $_POST['kesimpulan'];
    
    // Simpan data ke tabel resume_rapat
    $stmt = $conn->prepare("INSERT INTO resume_rapat (tanggal, pukul, tempat, acara, kesimpulan) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $tanggal, $pukul, $tempat, $acara, $kesimpulan);
    $stmt->execute();
    $resume_id = $stmt->insert_id;
    $stmt->close();
    
    // Simpan daftar hadir
    if (!empty($_POST['nama'])) {
        $stmt = $conn->prepare("INSERT INTO daftar_hadir (resume_id, nama, instansi, paraf_ttd) VALUES (?, ?, ?, ?)");
        foreach ($_POST['nama'] as $key => $nama) {
            $instansi = $_POST['instansi'][$key];
            $paraf_ttd = $_POST['paraf_ttd'][$key];
            $stmt->bind_param("isss", $resume_id, $nama, $instansi, $paraf_ttd);
            $stmt->execute();
        }
        $stmt->close();
    }
    
    echo "<script>alert('Data berhasil disimpan!'); window.location.href='index.php';</script>";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resume Rapat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-lite.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-lite.min.js"></script>
    <style>
        canvas {
            border: 1px solid #000;
            width: 100%;
            height: 150px;
            touch-action: none; /* Mencegah scrolling saat menggambar */
        }
    </style>
</head>
<body class="container mt-4">
    <h2 class="text-center">RESUME RAPAT</h2>
    <form id="form-resume" method="POST">
        <div class="row mb-3">
            <div class="col-md-3">
                <label>Tanggal:</label>
                <input type="date" class="form-control" name="tanggal" required>
            </div>
            <div class="col-md-3">
                <label>Pukul:</label>
                <input type="time" class="form-control" name="pukul" required>
            </div>
            <div class="col-md-3">
                <label>Tempat:</label>
                <input type="text" class="form-control" name="tempat" required>
            </div>
            <div class="col-md-3">
                <label>Acara:</label>
                <input type="text" class="form-control" name="acara" required>
            </div>
        </div>
        <div class="mb-3">
            <label>Kesimpulan:</label>
            <textarea class="form-control" id="kesimpulan" name="kesimpulan" required></textarea>
        </div>
        <h4>Daftar Hadir</h4>
        <div id="daftar-hadir"></div>
        <button type="button" class="btn btn-warning mb-3" onclick="tambahPeserta()">Tambah Peserta</button>
        <br><br>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="preview.php" target="_blank" class="btn btn-success">Preview</a>
    </form>

    <script>
        $(document).ready(function() {
            $('#kesimpulan').summernote({
                height: 150
            });
        });

        function tambahPeserta() {
            let id = $('.peserta').length;
            $('#daftar-hadir').append(`
                <div class="row mb-3 peserta">
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="nama[]" placeholder="Nama" required>
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="instansi[]" placeholder="Instansi" required>
                    </div>
                    <div class="col-md-4">
                        <canvas id="signatureCanvas${id}" width="300" height="150" class="border"></canvas>
                        <input type="hidden" name="paraf_ttd[]" id="paraf_ttd${id}">
                        <button type="button" class="btn btn-danger btn-sm mt-2" onclick="hapusPeserta(this)">Hapus</button>
                    </div>
                </div>
            `);
            setupCanvas(id);
        }

        function setupCanvas(id) {
            var canvas = document.getElementById("signatureCanvas" + id);
            var ctx = canvas.getContext("2d");
            var drawing = false;
            var rect = canvas.getBoundingClientRect(); // Ambil ukuran dan posisi canvas relatif terhadap viewport

            function getPosition(event) {
                let x, y;
                if (event.touches) {
                    let touch = event.touches[0];
                    x = touch.clientX - rect.left;
                    y = touch.clientY - rect.top;
                } else {
                    x = event.clientX - rect.left;
                    y = event.clientY - rect.top;
                }
                return { 
                    x: x * (canvas.width / rect.width), 
                    y: y * (canvas.height / rect.height) 
                };
            }

            function startDraw(event) {
                drawing = true;
                let pos = getPosition(event);
                ctx.beginPath();
                ctx.moveTo(pos.x, pos.y);
                event.preventDefault();
            }

            function draw(event) {
                if (!drawing) return;
                let pos = getPosition(event);
                ctx.lineTo(pos.x, pos.y);
                ctx.stroke();
                event.preventDefault();
            }

            function stopDraw() {
                drawing = false;
                document.getElementById("paraf_ttd" + id).value = canvas.toDataURL("image/png");
            }

            // Event listener untuk mouse
            canvas.addEventListener("mousedown", startDraw);
            canvas.addEventListener("mousemove", draw);
            canvas.addEventListener("mouseup", stopDraw);
            canvas.addEventListener("mouseleave", stopDraw);

            // Event listener untuk layar sentuh
            canvas.addEventListener("touchstart", startDraw, { passive: false });
            canvas.addEventListener("touchmove", draw, { passive: false });
            canvas.addEventListener("touchend", stopDraw);
        }

        function hapusPeserta(button) {
            $(button).closest('.peserta').remove();
        }
    </script>
</body>
</html>