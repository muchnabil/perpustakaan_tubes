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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Buku - Sistem Perpustakaan</title>
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
                <h1 class="book-title"><i class="fas fa-edit"></i> Edit Data Buku</h1>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?= $error ?></div>
            <?php endif; ?>
            
            <!-- Edit Book Form -->
            <div class="book-form">
                <h2><i class="fas fa-book"></i> Form Edit Buku</h2>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="judul">Judul Buku</label>
                        <input type="text" id="judul" name="judul" class="form-control" value="<?= htmlspecialchars($book['judul']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="pengarang">Pengarang</label>
                        <input type="text" id="pengarang" name="pengarang" class="form-control" value="<?= htmlspecialchars($book['pengarang']) ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="penerbit">Penerbit</label>
                        <input type="text" id="penerbit" name="penerbit" class="form-control" value="<?= htmlspecialchars($book['penerbit']) ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="tahun_terbit">Tahun Terbit</label>
                        <input type="number" id="tahun_terbit" name="tahun_terbit" class="form-control" min="1900" max="<?= date('Y') ?>" value="<?= htmlspecialchars($book['tahun_terbit']) ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="jumlah_stok">Jumlah Stok</label>
                        <input type="number" id="jumlah_stok" name="jumlah_stok" class="form-control" min="0" value="<?= htmlspecialchars($book['jumlah_stok']) ?>" required>
                    </div>
                    
                    <button type="submit" name="update_book" class="btn btn-submit">
                        <i class="fas fa-save"></i> Update Buku
                    </button>
                    <a href="buku.php" class="btn btn-cancel">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </form>
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