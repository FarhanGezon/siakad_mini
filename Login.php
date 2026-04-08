<?php
session_start();

if (!isset($_SESSION['akun_terdaftar'])) {
    // Akun default bawaan sistem
    $_SESSION['akun_terdaftar'] = [
        'admin' => 'admin'
    ];
}

if (isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

$mode = isset($_GET['mode']) ? $_GET['mode'] : 'login';
$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['aksi_login'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        if (isset($_SESSION['akun_terdaftar'][$username]) && $_SESSION['akun_terdaftar'][$username] === $password) {
            $_SESSION['login'] = true;
            header("Location: index.php");
            exit;
        } else {
            $error = "Username belum terdaftar atau password salah!";
        }
    } 
    elseif (isset($_POST['aksi_daftar'])) {
        $new_username = $_POST['new_username'];
        $new_password = $_POST['new_password'];

        if (isset($_SESSION['akun_terdaftar'][$new_username])) {
            $error = "Username sudah digunakan, silakan pilih yang lain!";
        } else {
            $_SESSION['akun_terdaftar'][$new_username] = $new_password;
            $success = "Akun berhasil dibuat! Silakan login.";
            $mode = 'login'; 
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $mode == 'daftar' ? 'Daftar' : 'Login' ?> · SIAKAD MINI</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .login-card { max-width: 400px; width: 100%; margin: 20px; }
        .toggle-link { text-align: center; margin-top: 20px; font-size: 0.9rem; }
        .toggle-link a { color: var(--primary); text-decoration: none; font-weight: 600; }
        .toggle-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="card login-card animate-fadeInUp">
        <div class="card-header" style="text-align: center; border: none; padding-bottom: 0;">
            <h1 style="color: var(--text-primary); margin-bottom: 5px;">🎓 SIAKAD</h1>
            <p style="color: var(--text-muted); margin-top: 0;">
                <?= $mode == 'daftar' ? 'Daftar akun baru' : 'Silakan login untuk melanjutkan' ?>
            </p>
        </div>
        
        <?php if($error): ?>
            <div style="background: var(--danger-light, #fee2e2); color: var(--danger, #b91c1c); padding: 10px; border-radius: 8px; margin: 15px 0; text-align: center; font-size: 0.85rem; font-weight: 600;">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <?php if($success): ?>
            <div style="background: #dcfce7; color: #166534; padding: 10px; border-radius: 8px; margin: 15px 0; text-align: center; font-size: 0.85rem; font-weight: 600;">
                <?= $success ?>
            </div>
        <?php endif; ?>

        <?php if($mode == 'login'): ?>
            <form method="post">
                <div class="form-group" style="margin-top: 20px;">
                    <label>Username</label>
                    <input type="text" name="username" placeholder="Masukkan username" required autofocus>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Masukkan password" required>
                </div>
                <button type="submit" name="aksi_login" class="btn btn-primary" style="margin-top: 10px;">Masuk Aplikasi</button>
            </form>
            <div class="toggle-link">
                <span style="color: var(--text-muted);">Belum punya akun?</span> 
                <a href="?mode=daftar">Daftar sekarang</a>
            </div>

        <?php else: ?>
            <form method="post">
                <div class="form-group" style="margin-top: 20px;">
                    <label>Username Baru</label>
                    <input type="text" name="new_username" placeholder="Buat username" required autofocus>
                </div>
                <div class="form-group">
                    <label>Password Baru</label>
                    <input type="password" name="new_password" placeholder="Buat password" required>
                </div>
                <button type="submit" name="aksi_daftar" class="btn btn-primary" style="margin-top: 10px;">Daftar Akun</button>
            </form>
            <div class="toggle-link">
                <span style="color: var(--text-muted);">Sudah punya akun?</span> 
                <a href="?mode=login">Kembali Login</a>
            </div>
        <?php endif; ?>

    </div>
</body>
</html>
