<?php
session_start();
require_once 'db.php';

// Ambil ID buku dari parameter URL
$id = intval($_GET['id']);

// Ambil data buku berdasarkan ID
$sql = "SELECT * FROM buku WHERE id_buku = $id";
$result = $conn->query($sql);
$book = $result->fetch_assoc();

// Variabel pesan
$message = '';
$error = '';

// Proses update jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_book'])) {
    $judul = $conn->real_escape_string($_POST['judul']);
    $pengarang = $conn->real_escape_string($_POST['pengarang']);
    $penerbit = $conn->real_escape_string($_POST['penerbit']);
    $tahun_terbit = !empty($_POST['tahun_terbit']) ? intval($_POST['tahun_terbit']) : 'NULL';
    $jumlah_stok = intval($_POST['jumlah_stok']);

    $sql = "UPDATE buku SET 
            judul = '$judul',
            pengarang = '$pengarang',
            penerbit = '$penerbit',
            tahun_terbit = $tahun_terbit,
            jumlah_stok = $jumlah_stok
            WHERE id_buku = $id";

    if ($conn->query($sql)) {
        header("Location: buku.php?update=success");
        exit();
    } else {
        $error = "Gagal memperbarui buku: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Buku - Sistem Perpustakaan</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Edit Data Buku</h2>

    <?php if ($error): ?>
        <p style="color: red;"><?= $error ?></p>
    <?php elseif ($message): ?>
        <p style="color: green;"><?= $message ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Judul:</label><br>
        <input type="text" name="judul" value="<?= htmlspecialchars($book['judul']) ?>" required><br><br>

        <label>Pengarang:</label><br>
        <input type="text" name="pengarang" value="<?= htmlspecialchars($book['pengarang']) ?>" required><br><br>

        <label>Penerbit:</label><br>
        <input type="text" name="penerbit" value="<?= htmlspecialchars($book['penerbit']) ?>" required><br><br>

        <label>Tahun Terbit:</label><br>
        <input type="number" name="tahun_terbit" value="<?= htmlspecialchars($book['tahun_terbit']) ?>"><br><br>

        <label>Jumlah Stok:</label><br>
        <input type="number" name="jumlah_stok" value="<?= htmlspecialchars($book['jumlah_stok']) ?>" required><br><br>

        <button type="submit" name="update_book">Update Buku</button>
        <a href="buku.php">Kembali</a>
    </form>
</body>
</html>
