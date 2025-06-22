<?php
session_start();
require_once 'db.php';

// Database operations
$message = '';
$error = '';

// Add new book
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_book'])) {
    $judul = $conn->real_escape_string($_POST['judul']);
    $pengarang = $conn->real_escape_string($_POST['pengarang']);
    $penerbit = $conn->real_escape_string($_POST['penerbit']);
    $tahun_terbit = !empty($_POST['tahun_terbit']) ? intval($_POST['tahun_terbit']) : 'NULL';
    $jumlah_stok = intval($_POST['jumlah_stok']);
    
    $sql = "INSERT INTO buku (judul, pengarang, penerbit, tahun_terbit, jumlah_stok) 
            VALUES ('$judul', '$pengarang', '$penerbit', $tahun_terbit, $jumlah_stok)";
    
    if ($conn->query($sql)) {
        $message = "Buku berhasil ditambahkan!";
    } else {
        $error = "Gagal menambahkan buku: " . $conn->error;
    }
}

// Delete book
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $sql = "DELETE FROM buku WHERE id_buku = $id";
    
    if ($conn->query($sql)) {
        $message = "Buku berhasil dihapus!";
    } else {
        $error = "Gagal menghapus buku: " . $conn->error;
    }
}

// Get all books
$sql = "SELECT * FROM buku ORDER BY id_buku DESC";
$result = $conn->query($sql);
$books = $result->fetch_all(MYSQLI_ASSOC);

// Query untuk data tahun terbit
$queryTahun = "SELECT tahun_terbit AS tahun, SUM(jumlah_stok) AS stok 
               FROM buku 
               GROUP BY tahun_terbit 
               ORDER BY tahun_terbit DESC";
$resultTahun = $conn->query($queryTahun);
$dataTahun = [];

if ($resultTahun && $resultTahun->num_rows > 0) {
    $dataTahun = $resultTahun->fetch_all(MYSQLI_ASSOC);
}

// Proses sorting ascending jika diminta
if (isset($_GET['sort_tahun']) && $_GET['sort_tahun'] == 'asc') {
    usort($dataTahun, function($a, $b) {
        return $a['tahun'] - $b['tahun'];
    });
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Buku - Sistem Perpustakaan</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --primary-dark: #3a0ca3;
            --danger: #f72585;
            --success: #4cc9f0;
            --warning: #f8961e;
            --white: #ffffff;
            --light: rgba(255,255,255,0.8);
            --dark: #1a1a2e;
            --gray: #6c757d;
            --light-gray: #f8f9fa;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header Styles */
        .main-header {
            background-color: #2c3e50;
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .main-nav ul {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        
        .main-nav li {
            margin-left: 1.5rem;
        }
        
        .main-nav a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
            padding: 0.5rem 0;
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.9rem;
        }
        
        .main-nav a:hover {
            color: #3498db;
        }

        /* Main Content */
        .book-management {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 30px;
        }

        .book-header {
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }

        .book-title {
            font-size: 1.8rem;
            color: #2c3e50;
            margin: 0;
        }

        /* Alert Messages */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 0.9rem;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        /* Form Styles */
        .book-form {
            background-color: #f8f9fa;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 30px;
        }

        .book-form h2 {
            font-size: 1.4rem;
            margin-bottom: 20px;
            color: #2c3e50;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #495057;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: #4361ee;
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
        }

        .btn {
            padding: 12px 20px;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-submit {
            background-color: #4361ee;
            color: white;
        }

        .btn-submit:hover {
            background-color: #3a0ca3;
            transform: translateY(-2px);
        }

        .btn-cancel {
            background-color: #6c757d;
            color: white;
            text-decoration: none;
        }

        .btn-cancel:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
        }

        /* Table Styles */
        .book-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 0.9rem;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }

        .book-table th {
            background: linear-gradient(to right, #2c3e50, #3498db);
            color: white;
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
        }

        .book-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }

        .book-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .book-table tr:hover {
            background-color: #f1f5fd;
        }

        .book-actions {
            display: flex;
            gap: 10px;
        }

        .btn-edit {
            background-color: #17a2b8;
            color: white;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 0.8rem;
        }

        .btn-edit:hover {
            background-color: #138496;
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 0.8rem;
        }

        .btn-delete:hover {
            background-color: #c82333;
        }

        /* Sorting Section */
        .tahun-terbit-section {
            margin-top: 40px;
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .tahun-terbit-section h3 {
            color: #2c3e50;
            margin-bottom: 15px;
        }
        
        .tahun-terbit-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        .tahun-terbit-table th, 
        .tahun-terbit-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .tahun-terbit-table th {
            background: linear-gradient(to right, #2c3e50, #3498db);
            color: white;
        }
        
        .tahun-terbit-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        
        .sort-btn-tahun {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 0.9rem;
        }
        
        .sort-btn-tahun:hover {
            background-color: #2980b9;
        }

        /* Footer Styles */
        .main-footer {
            background-color: #2c3e50;
            color: white;
            text-align: center;
            padding: 1.5rem 0;
            margin-top: 2rem;
            border-radius: 0 0 10px 10px;
        }

        .main-footer p {
            margin: 0;
            font-size: 0.9rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
            }
            
            .main-nav ul {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .main-nav li {
                margin: 0 10px 5px 0;
            }
            
            .book-actions {
                flex-direction: column;
                gap: 5px;
            }
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <header class="main-header">
        <div class="header-content">
            <a href="afterlogin.php" class="logo">
                <i class="fas fa-book"></i> Perpustakaan
            </a>
            <nav class="main-nav">
                <ul>
                    <li><a href="afterlogin.php"><i class="fas fa-home"></i> Beranda</a></li>
                    <li><a href="buku.php"><i class="fas fa-book"></i> Data Buku</a></li>
                    <li><a href="peminjaman.php"><i class="fas fa-hand-holding-heart"></i> Peminjaman</a></li>
                    <li><a href="pengembalian.php"><i class="fas fa-exchange-alt"></i> Pengembalian</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container">
        <div class="book-management">
            <div class="book-header">
                <h1 class="book-title"><i class="fas fa-book-open"></i> Manajemen Buku</h1>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><?= $message ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?= $error ?></div>
            <?php endif; ?>
            
            <!-- Add Book Form -->
            <div id="add-book-form" class="book-form">
                <h2><i class="fas fa-plus-circle"></i> Tambah Buku Baru</h2>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="judul">Judul Buku</label>
                        <input type="text" id="judul" name="judul" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="pengarang">Pengarang</label>
                        <input type="text" id="pengarang" name="pengarang" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="penerbit">Penerbit</label>
                        <input type="text" id="penerbit" name="penerbit" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="tahun_terbit">Tahun Terbit</label>
                        <input type="number" id="tahun_terbit" name="tahun_terbit" class="form-control" min="1900" max="<?= date('Y') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="jumlah_stok">Jumlah Stok</label>
                        <input type="number" id="jumlah_stok" name="jumlah_stok" class="form-control" min="0" required>
                    </div>
                    
                    <button type="submit" name="add_book" class="btn btn-submit">
                        <i class="fas fa-save"></i> Simpan Buku
                    </button>
                    <a href="buku.php" class="btn btn-cancel">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </form>
            </div>
            
            <!-- Books Table -->
            <table class="book-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Judul</th>
                        <th>Pengarang</th>
                        <th>Penerbit</th>
                        <th>Tahun Terbit</th>
                        <th>Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            <tbody>
                <?php if (count($books) > 0): ?>
                    <?php $no = 1; foreach ($books as $book): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($book['judul']) ?></td>
                            <td><?= htmlspecialchars($book['pengarang']) ?></td>
                            <td><?= $book['penerbit'] ? htmlspecialchars($book['penerbit']) : '-' ?></td>
                            <td><?= $book['tahun_terbit'] ? $book['tahun_terbit'] : '-' ?></td>
                            <td><?= $book['jumlah_stok'] ?></td>
                            <td>
                                <div class="book-actions">
                                    <a href="edit_buku.php?id=<?= $book['id_buku'] ?>" class="btn-edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="buku.php?delete=<?= $book['id_buku'] ?>" class="btn-delete" 
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus buku ini?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center;">Tidak ada data buku</td>
                    </tr>
                <?php endif; ?>
            </tbody>
            </table>

            <!-- Sorting Section -->
            <div class="tahun-terbit-section">
                <h3><i class="fas fa-chart-bar"></i> Statistik Tahun Terbit</h3>
                
                <?php if (!empty($dataTahun)): ?>
                    <a href="?sort_tahun=asc" class="sort-btn-tahun">
                        <i class="fas fa-sort"></i> Sorting
                    </a>
                    
                    <table class="tahun-terbit-table">
                        <thead>
                            <tr>
                                <th>TAHUN TERBIT</th>
                                <th>JUMLAH STOK</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dataTahun as $tahun): ?>
                                <tr>
                                    <td><?= htmlspecialchars($tahun['tahun']) ?></td>
                                    <td><?= htmlspecialchars($tahun['stok']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>Belum ada data tahun terbit yang tersedia.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer Section -->
    <footer class="main-footer">
        <p>&copy; <?= date('Y') ?> Sistem Perpustakaan. All rights reserved.</p>
    </footer>
</body>
</html>

<?php $conn->close(); ?>