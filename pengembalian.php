<?php
session_start();
require_once 'db.php';

$message = '';
$error = '';

if(isset($_POST['kembali'])) {
    $id_peminjaman = intval($_POST['id_peminjaman']);
    $tgl_kembali = $conn->real_escape_string($_POST['tgl_kembali']);
    
    // Update peminjaman
    $sql = "UPDATE peminjaman SET tanggal_kembali = '$tgl_kembali' WHERE id_peminjaman = $id_peminjaman";
    
    if ($conn->query($sql)) {
        $message = "Pengembalian berhasil!";
    } else {
        $error = "Gagal mengembalikan buku: " . $conn->error;
    }
}

// Get all loans that haven't been returned
$query = "SELECT p.*, b.judul 
          FROM peminjaman p
          LEFT JOIN buku b ON p.id_buku = b.id_buku
          WHERE p.tanggal_kembali IS NULL
          ORDER BY p.id_peminjaman DESC";
$result = $conn->query($query);
$loans = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengembalian Buku - Sistem Perpustakaan</title>
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
        .return-management {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 30px;
        }

        .return-header {
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }

        .return-title {
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
        .return-form {
            background-color: #f8f9fa;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 30px;
        }

        .return-form h2 {
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

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23333' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px 12px;
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

        .btn-primary {
            background-color: #4361ee;
            color: white;
        }

        .btn-primary:hover {
            background-color: #3a0ca3;
            transform: translateY(-2px);
        }

        /* Table Styles */
        .return-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 0.9rem;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }

        .return-table th {
            background: linear-gradient(to right, #2c3e50, #3498db);
            color: white;
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
        }

        .return-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }

        .return-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .return-table tr:hover {
            background-color: #f1f5fd;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 500;
            display: inline-block;
            text-transform: uppercase;
        }
        
        .status-dipinjam {
            background-color: #f39c12;
            color: white;
        }

        .return-actions {
            display: flex;
            gap: 10px;
        }

        .btn-success {
            background-color: #28a745;
            color: white;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 0.8rem;
        }

        .btn-success:hover {
            background-color: #218838;
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
            
            .return-actions {
                flex-direction: column;
                gap: 5px;
            }
            
            .return-table {
                display: block;
                overflow-x: auto;
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
        <div class="return-management">
            <div class="return-header">
                <h1 class="return-title"><i class="fas fa-exchange-alt"></i> Pengembalian Buku</h1>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><?= $message ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?= $error ?></div>
            <?php endif; ?>
            
            <!-- Form Pengembalian -->
            <div class="return-form">
                <h2><i class="fas fa-undo"></i> Proses Pengembalian</h2>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="id_peminjaman"><i class="fas fa-id-card"></i> ID Peminjaman</label>
                        <input type="number" id="id_peminjaman" name="id_peminjaman" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="tgl_kembali"><i class="fas fa-calendar-alt"></i> Tanggal Kembali</label>
                        <input type="date" id="tgl_kembali" name="tgl_kembali" class="form-control" required value="<?= date('Y-m-d') ?>">
                    </div>
                    
                    <button type="submit" name="kembali" class="btn btn-primary">
                        <i class="fas fa-check"></i> Proses Pengembalian
                    </button>
                </form>
            </div>
            
            <!-- Tabel Peminjaman Aktif -->
            <h3><i class="fas fa-list"></i> Daftar Peminjaman Aktif</h3>
            <table class="return-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Peminjam</th>
                        <th>Judul Buku</th>
                        <th>Jumlah</th>
                        <th>Tgl Pinjam</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($loans)): ?>
                        <?php foreach ($loans as $loan): ?>
                            <tr>
                                <td><?= $loan['id_peminjaman'] ?></td>
                                <td><?= htmlspecialchars($loan['nama_peminjam']) ?></td>
                                <td><?= htmlspecialchars($loan['judul'] ?? '-') ?></td>
                                <td><?= $loan['jumlah_pinjam'] ?></td>
                                <td><?= date('d/m/Y', strtotime($loan['tanggal_pinjam'])) ?></td>
                                <td>
                                    <span class="status-badge status-dipinjam">Dipinjam</span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">Tidak ada peminjaman aktif</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Footer Section -->
    <footer class="main-footer">
        <p>&copy; <?= date('Y') ?> Sistem Perpustakaan. All rights reserved.</p>
    </footer>
</body>
</html>

<?php $conn->close(); ?>