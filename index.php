<?php
//データベースから情報取得
require("config.php");
define('DB_DATABASE', $DB_DATABASE);
define('DB_USERNAME', $DB_USERNAME);
define('DB_PASSWORD', $DB_PASSWORD);
define('PDO_DSN', 'mysql:host=localhost;dbname=' . DB_DATABASE);

try {
    //DB接続
    $db = new PDO(PDO_DSN, DB_USERNAME, DB_PASSWORD);
    //エラーをスロー
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    //3時間分（5分ごとにラズパイから取得なので*36）
    $sql = "SELECT * FROM sensor order by datetime desc limit 36";
    $statement = $db->query($sql);

    //連想配列
    while ($row = $statement->fetch()) {
        $rows[] = $row;
    }
    //データベース接続切断
    $dbh = null;
} catch (PDOException $e) {
    echo $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>おうち観測</title>
  <link rel="shortcut icon" href="assets/img/favicon_outikansoku.png">

  <!-- BootstrapのCSS読み込み -->
  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
  <!-- jQuery読み込み -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <!-- BootstrapのJS読み込み -->
  <script src="assets/js/bootstrap.min.js"></script>
  <style>
    .btn {
      margin-right: 10px;
    }
  </style>
</head>

<body>
  <!--ナビゲーションメニュー-->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <a class="navbar-brand" href="">おうち観測　</a>
      <!--レスポンシブ-->
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="ナビゲーションの切替">
        <span class="navbar-toggler-icon"></span>
      </button>
      <!--メニュー項目-->
      <div class="collapse navbar-collapse" id="navbar">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item active">
            <a class="nav-link" href="">ホーム<span class="sr-only">(現位置)</span></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="./setting.php">設定</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <!--一覧-->
  <div class="container p-3">
    <p class="font-weight-bold">取得データ（3時間前まで表示）</p>
    <div class="table-responsive">
      <table bgcolor="#FFFFFF" class="table table-hover table-bordered  ">
        <thead>
          <tr>
            <th nowrap>id</th>
            <th nowrap>日時</th>
            <th nowrap>室温 </th>
            <th nowrap>湿度 </th>
          </tr>
        </thead>
        <tbody>
          <!-- 情報一覧表示 -->
          <?php
          foreach ($rows as $row) {
              ?>
            <tr>
              <td><?php echo $row['id']; ?></td>
              <td><?php echo htmlspecialchars($row['datetime'], ENT_QUOTES, 'UTF-8'); ?></td>
              <td><?php echo $row['temp']; ?></td>
              <td><?php echo $row['hum']; ?></td>
            </tr>
          <?php
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
