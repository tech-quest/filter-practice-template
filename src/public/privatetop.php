<?php
declare(strict_types=1);
require_once './pdoConnect.php';
$pdo = connect();
$pdo->query('SET NAMES UTF8');
$startDate = filter_input(INPUT_GET, 'date') . ' 00:00:00';
$endDate = filter_input(INPUT_GET, 'date') . ' 23:59:59';

if (isset($_GET['search'])) {
    $title = '%' . $_GET['search'] . '%';
    $content = '%' . $_GET['search'] . '%';
} else {
    $title = '%%';
    $content = '%%';
}

if (empty($_GET['date'])) {
    $startDate = '2022-05-03' . ' 00:00:00';
    $endDate = date('Y-m-d') . ' 23:59:59';
}

$sql = <<<EOF
  SELECT
    *
  FROM
    pages
  WHERE
    (title LIKE :title OR content LIKE :content)
  AND 
    (created_at BETWEEN :startDate AND :endDate)
EOF;

if ($_GET['order'] === 'desc') {
    //降順に並び替えるSQL文に変更
    $sql = $sql . 'ORDER BY created_at DESC';
} elseif ($_GET['order'] === 'asc') {
    //昇順に並び替えるSQL文に変更
    $sql = $sql . 'ORDER BY created_at ASC';
}

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
      <form action="privatetop.php" method="get">
        <!-- 検索ワード入力欄生成 -->
      <input name="search" type="text" value="<?php echo $_GET['search'] ??
          ''; ?>" placeholder="キーワードを入力" />
      <!-- type="date" ユーザーに日付を入力させる入力欄生成 -->
      <input name="date" type="date" />
        <div>
          <!-- 新着順のラジオボタン生成 -->
          <label>
            <input type="radio" name="order" value="desc" class=""
            <?php if (!isset($_GET['order']) || $_GET['order'] == 'desc') {
                echo 'checked';
            } ?>>
            <span>新着順</span>
          </label>
          <label>
            <input type="radio" name="order" value="asc" class=""
            <?php if (isset($_GET['order']) && $_GET['order'] != 'desc') {
                echo 'checked';
            } ?>>
            <span>古い順</span>
          </label>
        </div>
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