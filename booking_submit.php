<?php
// ── booking_submit.php ────────────────────────────────────────────────────────
// Handles the booking form submission and saves to MySQL (InfinityFree)
// ─────────────────────────────────────────────────────────────────────────────

$db_host = 'YOUR_HOST';          // Check your InfinityFree CP for exact host
$db_name = 'YOUR_DATABASE';
$db_user = 'YOUR_USERNAME';
$db_pass = 'YOUR_PASSWORD';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create table if it doesn't exist yet
    $pdo->exec("CREATE TABLE IF NOT EXISTS bookings (
        id          INT AUTO_INCREMENT PRIMARY KEY,
        name        VARCHAR(100) NOT NULL,
        phone       VARCHAR(20)  NOT NULL,
        email       VARCHAR(100),
        date        DATE         NOT NULL,
        time        VARCHAR(20),
        guests      VARCHAR(20),
        seating     VARCHAR(30),
        occasion    VARCHAR(50),
        notes       TEXT,
        status      ENUM('pending','accepted','rejected') DEFAULT 'pending',
        created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Sanitise inputs
    $name    = trim($_POST['name']     ?? '');
    $phone   = trim($_POST['phone']    ?? '');
    $email   = trim($_POST['email']    ?? '');
    $date    = trim($_POST['date']     ?? '');
    $time    = trim($_POST['time']     ?? '');
    $guests  = trim($_POST['guests']   ?? '');
    $seating = trim($_POST['seating']  ?? '');
    $occasion= trim($_POST['occasion'] ?? '');
    $notes   = trim($_POST['notes']    ?? '');

    if (empty($name) || empty($phone) || empty($date)) {
        header('Location: booking.php?error=missing_fields');
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO bookings
        (name, phone, email, date, time, guests, seating, occasion, notes)
        VALUES (:name,:phone,:email,:date,:time,:guests,:seating,:occasion,:notes)");

    $stmt->execute([
        ':name'     => $name,
        ':phone'    => $phone,
        ':email'    => $email,
        ':date'     => $date,
        ':time'     => $time,
        ':guests'   => $guests,
        ':seating'  => $seating,
        ':occasion' => $occasion,
        ':notes'    => $notes,
    ]);

    header('Location: booking.php?success=1');
    exit;

} catch (PDOException $e) {
    error_log('FLAMMA booking error: ' . $e->getMessage());
    header('Location: booking.php?error=db_error');
    exit;
}
