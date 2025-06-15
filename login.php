<?php
session_start();
$conn = new mysqli("localhost", "root", "", "career_crafters");

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil data dari form
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Validasi input dasar
if (empty($email) || empty($password) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Email atau password tidak valid.'); window.location.href='login.html';</script>";
    exit;
}

// Cari user berdasarkan email
$stmt = $conn->prepare("SELECT password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($hashedPasswordFromDB);
    $stmt->fetch();

    // Verifikasi password
    if (password_verify($password, $hashedPasswordFromDB)) {
        $_SESSION['email'] = $email;
        header("Location: dashboard.html");
        exit;
    } else {
        echo "<script>alert('Password salah.'); window.location.href='login.html';</script>";
    }
} else {
    echo "<script>alert('Email tidak ditemukan.'); window.location.href='login.html';</script>";
}

$stmt->close();
$conn->close();
?>
