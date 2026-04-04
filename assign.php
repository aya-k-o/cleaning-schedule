<?php
require 'db_connect.php';

$today = date('Y-m-d');

$check = $pdo->prepare("SELECT COUNT(*) FROM schedule WHERE date = :today");
$check->execute([':today' => $today]);
$count = $check->fetchColumn();

if($count > 0) {
  header('Location: index.php');
  exit;
}

$locations = $pdo->query("SELECT * FROM locations")->fetchAll();

$sql = "SELECT e.id, e.name, COUNT(s.id) AS count
        FROM employees e
        LEFT JOIN schedule s ON e.id = s.employee_id
        GROUP BY e.id, e.name
        ORDER BY count ASC";
$employees = $pdo->query($sql)->fetchAll();

foreach ($locations as $i => $location) {
  $employee = $employees[$i % count($employees)];
  $stmt = $pdo->prepare("INSERT INTO schedule (employee_id, location_id,date)
                         VALUES (:employee_id, :location_id, :today)");

$stmt->execute([
  ':employee_id' => $employee['id'],
  ':location_id' => $location['id'],
  ':today'       => $today,
]);
}

header('Location: index.php');
exit;