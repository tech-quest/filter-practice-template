<?php
declare(strict_types=1);
require_once './pdoConnect.php';
$pdo = connect();
$pdo->query('SET NAMES UTF8');
$startDate = filter_input(INPUT_GET, 'date') . ' 00:00:00';
$endDate = filter_input(INPUT_GET, 'date') . ' 23:59:59';

/* 日付が指定されていないときは、今日までの日付で全選択 */
if (empty($_GET['date'])) {
    $startDate = '2022-05-03' . ' 00:00:00';
    $endDate = date('Y-m-d') . ' 23:59:59';
}

$sql = 'SELECT * FROM pages WHERE created_at BETWEEN :startDate AND :endDate';
$statement = $pdo->prepare($sql);
$statement->bindValue(':startDate', $startDate, PDO::PARAM_STR);
$statement->bindValue(':endDate', $endDate, PDO::PARAM_STR);
$statement->execute();
$pages = $statement->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>top画面</title>
</head>

<body>
  <div>
    <div>
      <form action="" method="GET">
        <div>
        <input name="date" type="date" />
        </div>
        <button type="submit">送信</button>
      </form>
      <table border="1">
        <tr>
          <th>タイトル</th>
          <th>内容</th>
          <th>作成日時</th>
        </tr>
        <?php foreach ($pages as $page): ?>
          <tr>
            <td><?php echo $page['title']; ?></td>
            <td><?php echo $page['content']; ?></td>
            <td><?php echo $page['created_at']; ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>
  </div>
</body>

</html>