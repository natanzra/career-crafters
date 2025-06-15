mysqli_report(MYSQLI_REPORT_OFF); // Tambahkan ini untuk nonaktifkan mode exception

<?php
// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "career_crafters");

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil data dari form
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Validasi input sederhana
if (empty($email) || empty($password) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Email atau password tidak valid.'); window.location.href='regist.html';</script>";
    exit;
}

// Hash password dengan algoritma yang aman
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Cek apakah email sudah digunakan
$checkStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$checkStmt->bind_param("s", $email);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    echo "<script>alert('Email sudah digunakan.'); window.location.href='regist.html';</script>";
} else {
    // Insert user baru
    $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $email, $hashedPassword);

    if ($stmt->execute()) {
        echo "<script>alert('Registrasi berhasil!'); window.location.href='login.html';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan saat menyimpan.'); window.location.href='regist.html';</script>";
    }

    $stmt->close();
}

$checkStmt->close();
$conn->close();
?>
