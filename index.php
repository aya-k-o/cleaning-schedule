<?php
require 'db_connect.php';

$today = date('Y-m-d');

$sql = "SELECT e.name AS employee_name, l.name AS location_name
        FROM schedule s
        JOIN employees e ON s.employee_id = e.id
        JOIN locations l ON s.location_id = l.id
        WHERE s.date = :today";

$stmt = $pdo->prepare($sql);
$stmt->execute([':today' => $today]);
$results = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <title>掃除当番表</title>
</head>
<body>
  <div class="container">
    <header>
      <h1>今日の掃除当番</h1>
      <p>日付：<?= $today ?></p>
    </header>
    <div class="card">
      <table>
        <tr>
          <th>場所</th>
          <th>担当者</th>
        </tr>


<?php if (empty($results)): ?>
<tr>
  <td colspan="2">データがありません。当番を割り当ててください。</td>
</tr>
<?php else: ?>
<?php foreach ($results as $row): ?>
<tr>
  <td><?= htmlspecialchars($row['location_name']) ?></td>
  <td><span class="badge"><?= htmlspecialchars($row['employee_name']) ?></span></td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
            
      </table>
      <div class="actions">
        <a href="assign.php">当番を割り当てる</a>
        <a href="admin.php">管理画面へ</a>
      </div>
    </div>
  </div>
</body>
</html>