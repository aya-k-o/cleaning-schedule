<?php
require 'db_connect.php';

$today = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>掃除当番表</title>
</head>
<body>
  <h1>今日の掃除当番</h1>
  <p>日付：<?= $today ?></p>

  <?php
  $sql = "SELECT e.name AS employee_name, l.name AS location_name
          FROM schedule s
          JOIN employees e ON s.employee_id = e.id
          JOIN locations l ON s.location_id = l.id
          WHERE s.date = :today";

  $stmt = $pdo->prepare($sql);
  $stmt->execute([':today' => $today]);
  $results = $stmt->fetchAll();
  ?>

  <table border="1">
    <tr>
      <th>場所</th>
      <th>担当者</th>
    </tr>
    <?php foreach ($results as $row): ?>
    <tr>
      <td><?= htmlspecialchars($row['location_name']) ?></td>
      <td><?= htmlspecialchars($row['employee_name']) ?></td>
    </tr>
    <?php endforeach; ?>
  </table>

</body>
</html>