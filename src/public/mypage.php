<?php
declare(strict_types=1);
require_once './pdoConnect.php';
$pdo = connect();
$pdo->query('SET NAMES UTF8');

if (isset($_GET['search'])) {
    $title = '%' . $_GET['search'] . '%';
    $content = '%' . $_GET['search'] . '%';
} else {
    $title = '%%';
    $content = '%%';
}

/* $startDateが入力されていなかったら、0000-00-00 00:00:00以降に作成されたメモ、
 $endDateが入力されていなかったら、今日の23:59:59以前に作成されたメモで絞り込み */

if (!empty($_GET['startDate']) && !empty($_GET['endDate'])) {
    $startDate = filter_input(INPUT_GET, 'startDate') . ' 00:00:00';
    $endDate = filter_input(INPUT_GET, 'endDate') . ' 23:59:59';
}
if (empty($_GET['startDate']) && !empty($_GET['endDate'])) {
    // 0000-00-00 00:00:00は使わない方が良さそうなので初回のメモ日で代用
    $startDate = '2022-05-03' . ' 00:00:00';
    $endDate = filter_input(INPUT_GET, 'endDate') . ' 23:59:59';
}
if (!empty($_GET['startDate']) && empty($_GET['endDate'])) {
    $startDate = filter_input(INPUT_GET, 'startDate') . ' 00:00:00';
    // 今日の23:59:59
    $endDate = date('Y-m-d') . ' 23:59:59';
}
if (empty($_GET['startDate']) && empty($_GET['endDate'])) {
    $startDate = '2022-05-03' . ' 00:00:00';
    // 今日の23:59:59
    $endDate = date('Y-m-d') . ' 23:59:59';
}

$sql = <<<EOF
  SELECT
    *
  FROM
    pages
  WHERE
    (title LIKE :title OR content LIKE :content) AND (created_at BETWEEN :startDate AND :endDate);
EOF;

$statement = $pdo->prepare($sql);
$statement->bindValue(':title', $title, PDO::PARAM_STR);
$statement->bindValue(':content', $content, PDO::PARAM_STR);
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
      <input name="search" type="text" value="<?php echo $_GET['search'] ??
          ''; ?>" placeholder="キーワードを入力" />
      <!-- type="date" ユーザーに日付を入力させる入力欄生成 -->
      <input name="startDate" type="date" />
      <input name="endDate" type="date" />
        <button type="submit">送信</button>
      </form>
    </div>
    
    <div>
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