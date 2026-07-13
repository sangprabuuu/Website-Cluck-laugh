<?php
session_start();
include '../koneksi.php';

if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

// Pastikan akun admin default tersedia
mysqli_query(
    $conn,
    "INSERT INTO admin (username, password)
     SELECT 'admin', 'admin123'
     WHERE NOT EXISTS (SELECT 1 FROM admin WHERE username = 'admin')"
);

if (isset($_POST['login'])) {
    $user = trim($_POST['username'] ?? '');
    $pass = trim($_POST['password'] ?? '');

    $stmt = mysqli_prepare($conn, "SELECT password FROM admin WHERE username = ? LIMIT 1");

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $user);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $dbPass);

        $isValid = false;
        if (mysqli_stmt_fetch($stmt)) {
            // Dukung password plain text maupun hash
            $isValid = ($pass === $dbPass) || password_verify($pass, $dbPass);
        }

        mysqli_stmt_close($stmt);

        if ($isValid) {
            $_SESSION['login'] = true;
            $_SESSION['username'] = $user;
            header('Location: dashboard.php');
            exit;
        }
    }

    if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
        $error = 'Login gagal! Username atau password salah.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../navbar.php'; ?>

<div class="container">
    <h1>Login Admin</h1>

    <?php if ($error !== '') { ?>
        <div class="error"><?= htmlspecialchars($error); ?></div>
    <?php } ?>

    <form method="POST" class="card">
        <label>Username</label>
        <input type="text" name="username" placeholder="Username" required>

        <label>Password</label>
        <input type="password" name="password" placeholder="Password" required>

        <button name="login" type="submit">Login</button>
    </form>
</div>

<script src="../assets/js/script.js"></script>
</body>
</html>
