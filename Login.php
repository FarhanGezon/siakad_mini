<?php
session_start();

if (isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

$error = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username === "admin" && $password === "admin") {
        $_SESSION['login'] = true;
        header("Location: index.php");
        exit;
    } else {
        $error = true;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login · SIAKAD MINI</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
        .login-card { max-width: 400px; width: 100%; margin: 20px; }
    </style>
</head>
<body>
    <div class="card login-card animate-fadeInUp">
        <div class="card-header" style="text-align: center; border: none;">
            <h1 style="color: var(--navy); margin-bottom: 5px;">🎓 SIAKAD</h1>
            <p style="color: #64748b; margin-top: 0;">Silakan login untuk melanjutkan</p>
        </div>
        
        <?php if($error): ?>
            <div style="background: #fee2e2; color: #b91c1c; padding: 10px; border-radius: 8px; margin-bottom: 15px; text-align: center; font-size: 0.85rem;">
                Username atau password salah!
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Ketik: admin" required autofocus>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Ketik: admin" required>
            </div>
            <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Masuk Aplikasi</button>
        </form>
    </div>
</body>
</html>
