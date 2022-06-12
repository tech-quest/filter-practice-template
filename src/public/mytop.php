<?php
declare(strict_types=1);
require_once './pdoConnect.php';
try {
    $pdo = connect();
    $pdo->query('SET NAMES UTF8');

    if (isset($_GET['word']) && isset($_GET['date'])) {
        $search_word = $_GET['word'];
        $search_date = $_GET['date'];
    } else {
        $search_word = '%%';
        $search_date = '%%';
    }

    $sql =
        "SELECT * FROM pages WHERE content LIKE '%" .
        $search_word .
        "%' AND created_at LIKE '%" .
        $search_date .
        "%' OR title LIKE '%" .
        $search_word .
        "%' AND created_at LIKE '%" .
        $search_date .
        "%'";
    $statement = $pdo->prepare($sql);
    $statement->execute();
    $memos = $statement->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'DB接続エラー' . $e->getMessage();
}
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
      <input type="text" name="word" placeholder="Search..."/>
      <input type="date" name="date" placeholder="2022-05-04">
    </div>
    <input type="submit" value="検索"/>
    </form>
  </div>
    
    <div>
      <table border="1">
        <tr>
          <th>タイトル</th>
          <th>内容</th>
          <th>作成日時</th>
        </tr>
        <?php foreach ($memos as $memo): ?>
          <tr>
            <td><?php echo $memo['title']; ?></td>
            <td><?php echo $memo['content']; ?></td>
            <td><?php echo $memo['created_at']; ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>
  </div>
</body>

</html>