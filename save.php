<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: text/html; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Method not allowed";
    exit;
}

$vorname = trim($_POST['vorname'] ?? '');
$nachname = trim($_POST['nachname'] ?? '');
$datum = trim($_POST['datum'] ?? '');

if ($vorname === '' || $nachname === '' || $datum === '') {
    echo "Bitte alle Felder ausfüllen.";
    exit;
}
if (!preg_match('/^[A-Za-zÄÖÜäöüß]+$/u', $vorname) || !preg_match('/^[A-Za-zÄÖÜäöüß]+$/u', $nachname)) {
    echo "Ungültiger Name. Nur Buchstaben erlaubt.";
    exit;
}
$date = DateTime::createFromFormat('Y-m-d', $datum);
if (!$date || $date->format('Y-m-d') !== $datum) {
    echo "Ungültiges Datum.";
    exit;
}


require_once __DIR__ . '/db.php'; 
$savedTo = null;
if (isset($pdo) && $pdo instanceof PDO) {
    try {
        $stmt = $pdo->prepare('INSERT INTO personen (vorname, nachname, geburtstag) VALUES (:v, :n, :d)');
        $stmt->execute([':v' => $vorname, ':n' => $nachname, ':d' => $datum]);
        $savedTo = 'mysql';
    } catch (Exception $e) {
        $savedTo = 'db-error';
    }
}

if ($savedTo !== 'mysql') {
    $filepath = __DIR__ . '/daten.csv';
    $fp = fopen($filepath, 'a');
    if ($fp) {
        $line = [$vorname, $nachname, $datum, date('Y-m-d H:i:s')];
        fputcsv($fp, $line);
        fclose($fp);
        $savedTo = 'csv';
    } else {
        echo "Fehler beim Speichern.";
        exit;
    }
}
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Daten gespeichert</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f5f5f5; color:#222; display:flex; align-items:center; justify-content:center; height:100vh; margin:0 }
        .card{ background:#fff; padding:2rem; border-radius:10px; box-shadow:0 6px 24px rgba(0,0,0,0.08); text-align:center }
        a{ color:#0066ff; text-decoration:none; font-weight:bold }
    </style>
</head>
<body>
    <div class="card">
        <h1>Erfolgreich gespeichert ✅</h1>
        <p>Vorname: <?php echo htmlspecialchars($vorname, ENT_QUOTES); ?><br>
        Nachname: <?php echo htmlspecialchars($nachname, ENT_QUOTES); ?><br>
        Datum: <?php echo htmlspecialchars($datum, ENT_QUOTES); ?></p>
        <p>Gespeichert in: <?php echo $savedTo === 'mysql' ? 'MySQL-Datenbank' : 'daten.csv (Fallback)'; ?></p>
        <p><a href="regrestieren.html">Zurück zum Formular</a> • <a href="daten.php">Alle Einträge anzeigen</a></p>
    </div>
</body>
</html>