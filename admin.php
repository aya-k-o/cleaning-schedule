<?php
require 'db_connect.php';

if (isset($_POST['add_employee'])) {
    $stmt = $pdo->prepare("INSERT INTO employees (name) VALUES (:name)");
    $stmt->execute([':name' => $_POST['employee_name']]);
}

if (isset($_POST['delete_employee'])) {
    $stmt = $pdo->prepare("UPDATE employees SET is_active = 0 WHERE id = :id");
    $stmt->execute([':id' => $_POST['employee_id']]);
}


if (isset($_POST['permanent_delete_employee'])) {
    $stmt = $pdo->prepare("DELETE FROM schedule WHERE employee_id = :id");
    $stmt->execute([':id' => $_POST['employee_id']]);
    
    $stmt = $pdo->prepare("DELETE FROM employees WHERE id = :id");
    $stmt->execute([':id' => $_POST['employee_id']]);
}

if (isset($_POST['add_location'])) {
    $stmt = $pdo->prepare("INSERT INTO locations (name) VALUES (:name)");
    $stmt->execute([':name' => $_POST['location_name']]);
}

if (isset($_POST['delete_location'])) {
    $check = $pdo->prepare("SELECT COUNT(*) FROM schedule WHERE location_id = :id");
    $check->execute([':id' => $_POST['location_id']]);
    $count = $check->fetchColumn();

    if ($count > 0) {
        $location_error = 'この場所は当番履歴に登録されているため削除できません。';
    } else {
        $stmt = $pdo->prepare("DELETE FROM locations WHERE id = :id");
        $stmt->execute([':id' => $_POST['location_id']]);
    }
}

$employees = $pdo->query("SELECT * FROM employees WHERE is_active = 1")->fetchAll();
$retired = $pdo->query("SELECT * FROM employees WHERE is_active = 0")->fetchAll();
$locations = $pdo->query("SELECT * FROM locations")->fetchAll();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <title>管理画面</title>
</head>
<body>
  <div class="container">
    <header>
      <h1>管理画面</h1>
    </header>
    <div class="card">
      <h2>社員管理</h2>
      <?php if (!empty($employee_error)): ?>
        <p class="error"><?= $employee_error ?></p>
      <?php endif; ?>
      <form method="post">

        <div class="form-row">
          <input type="text" name="employee_name" placeholder="社員名">
          <button type="submit" name="add_employee">追加</button>
        </div>
      </form>
      <table>
        <tr>
          <th>ID</th>
          <th>名前</th>
          <th>操作</th>
        </tr>
        <?php foreach ($employees as $employee): ?>
        <tr>
          <td><?= $employee['id'] ?></td>
          <td><?= htmlspecialchars($employee['name']) ?></td>
          <td>
            <form method="post">
              <input type="hidden" name="employee_id" value="<?= $employee['id'] ?>">
              <button type="submit" name="delete_employee" class="delete-btn">退職</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </table>
      <?php if (!empty($retired)): ?>
      <h2>退職済み社員</h2>
      <table>
        <tr>
          <th>ID</th>
          <th>名前</th>
          <th>操作</th>
        </tr>
        <?php foreach ($retired as $employee): ?>
        <tr>
          <td><?= $employee['id'] ?></td>
          <td><?= htmlspecialchars($employee['name']) ?></td>
          <td>
            <form method="post">
              <input type="hidden" name="employee_id" value="<?= $employee['id'] ?>">
              <button type="submit" name="permanent_delete_employee" class="delete-btn" onclick="return confirm('本当に完全削除しますか？この操作は取り消せません。')">完全削除</button>
            </form>
          </td>
        </tr>
          <?php endforeach; ?>
        </table>
        <?php endif; ?>
    </div>
    <div class="card">
      <h2>場所管理</h2>
      <?php if (!empty($location_error)): ?>
        <p class="error"><?= $location_error ?></p>
      <?php endif; ?>
      <form method="post">
        <div class="form-row">
          <input type="text" name="location_name" placeholder="場所名">
          <button type="submit" name="add_location">追加</button>
        </div>
      </form>
      <table>
        <tr>
          <th>ID</th>
          <th>場所名</th>
          <th>操作</th>
        </tr>
        <?php foreach ($locations as $location): ?>
        <tr>
          <td><?= $location['id'] ?></td>
          <td><?= htmlspecialchars($location['name']) ?></td>
          <td>
            <form method="post">
              <input type="hidden" name="location_id" value="<?= $location['id'] ?>">
              <button type="submit" name="delete_location" class="delete-btn">削除</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </table>
    </div>
    <div class="actions">
      <a href="index.php">当番表に戻る</a>
    </div>
  </div>
</body>
</html>