<?php
session_start();
if(!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Perpustakaan</title>
    <style>
        :root {
            --primary: #4361ee;
            --primary-dark: #3a0ca3;
            --danger: #f72585;
            --success: #4cc9f0;
            --white: #ffffff;
            --light: rgba(255,255,255,0.8);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), 
                        url('https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80') no-repeat center center fixed;
            background-size: cover;
            color: var(--white);
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .dashboard-header {
            text-align: center;
            margin-bottom: 3rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .dashboard-title {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            background: linear-gradient(to right, var(--primary), var(--success));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .welcome-msg {
            font-size: 1.2rem;
            color: var(--light);
        }
        
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
        }
        
        .menu-card {
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(10px);
            padding: 2.5rem 2rem;
            border-radius: 15px;
            text-align: center;
            text-decoration: none;
            color: var(--white);
            transition: all 0.4s;
            border: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        
        .menu-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.4);
            border-color: var(--primary);
        }
        
        .menu-card i {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            background: linear-gradient(to bottom, var(--primary), var(--success));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .menu-card h3 {
            font-size: 1.5rem;
            margin-bottom: 0.8rem;
        }
        
        .menu-card p {
            color: var(--light);
            font-size: 0.95rem;
            line-height: 1.6;
        }
        
        .logout-card {
            border-color: rgba(247, 37, 133, 0.3);
        }
        
        .logout-card:hover {
            border-color: var(--danger);
            background: rgba(247, 37, 133, 0.1);
        }
        
        .logout-card i {
            background: linear-gradient(to bottom, var(--danger), #ff6b6b);
            -webkit-background-clip: text;
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <header class="dashboard-header">
            <h1 class="dashboard-title">Sistem Manajemen Perpustakaan</h1>
            <p class="welcome-msg">Selamat datang, <?= htmlspecialchars($_SESSION['admin']['nama'] ?? 'Admin') ?></p>
        </header>
        
        <div class="menu-grid">
            <a href="buku.php" class="menu-card">
                <i class="fas fa-book-open"></i>
                <h3>Kelola Buku</h3>
                <p>Manajemen Koleksi Buku Perpustakaan</p>
            </a>
            
            <a href="peminjaman.php" class="menu-card">
                <i class="fas fa-hand-holding-heart"></i>
                <h3>Peminjaman</h3>
                <p>Proses Peminjaman Buku</p>
            </a>
            
            <a href="pengembalian.php" class="menu-card">
                <i class="fas fa-exchange-alt"></i>
                <h3>Pengembalian</h3>
                <p>Proses Pengembalian Buku</p>
            </a>
            
            <a href="logout.php" class="menu-card logout-card">
                <i class="fas fa-sign-out-alt"></i>
                <h3>Logout</h3>
                <p>Keluar dari Sistem Admin</p>
            </a>
        </div>
    </div>
</body>
</html>