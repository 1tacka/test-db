<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/db.php';
header('Content-Type: text/html; charset=utf-8');

$rows = [];
$source = '';
if (isset($pdo) && $pdo instanceof PDO) {
    try {
        $stmt = $pdo->query('SELECT vorname, nachname, geburtstag, created_at FROM personen ORDER BY id DESC LIMIT 500');
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $source = 'MySQL';
    } catch (Exception $e) {
        $source = 'MySQL (error)';
    }
}

if ($source === '' || !$rows) {
    $file = __DIR__ . '/daten.csv';
    if (file_exists($file)) {
        $source = 'CSV';
        if (($fp = fopen($file, 'r')) !== false) {
            while (($data = fgetcsv($fp)) !== false) {
                $rows[] = [
                    'vorname' => $data[0] ?? '',
                    'nachname' => $data[1] ?? '',
                    'geburtstag' => $data[2] ?? '',
                    'created_at' => $data[3] ?? ''
                ];
            }
            fclose($fp);
        }
    }
}

?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Alle Einträge</title>
    <style>
        body{ font-family: Arial, sans-serif; margin: 2rem; }
        table{ border-collapse: collapse; width: 100%; max-width: 900px }
        th, td{ border: 1px solid #ddd; padding: 8px; text-align: left }
        th{ background: #f0f0f0 }
    </style>
</head>
<body>
    <h1>Gespeicherte Einträge</h1>
    <p>Quelle: <?php echo htmlspecialchars($source); ?></p>
    <?php if (empty($rows)): ?>
        <p>Keine Einträge gefunden.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr><th>Vorname</th><th>Nachname</th><th>Geburtstag</th><th>Erstellt</th></tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $r): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($r['vorname'] ?? '', ENT_QUOTES); ?></td>
                        <td><?php echo htmlspecialchars($r['nachname'] ?? '', ENT_QUOTES); ?></td>
                        <td><?php echo htmlspecialchars($r['geburtstag'] ?? '', ENT_QUOTES); ?></td>
                        <td><?php echo htmlspecialchars($r['created_at'] ?? '', ENT_QUOTES); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <p><a href="regrestieren.html">Zurück</a></p>
</body>
</html>