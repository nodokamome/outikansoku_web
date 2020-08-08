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

    //設定更新
    if ($_POST["good_low_temp"] != "") {
        $set1 = $_POST["good_low_temp"];
        $set2 = $_POST["good_hi_temp"];
        $set3 = $_POST["good_low_hum"];
        $set4 = $_POST["good_hi_hum"];
        $set5 = $_POST["danger_hi_temp"];
        $set6 = $_POST["led_start_hour"];
        $set7 = $_POST["led_end_hour"];

        $sql = "UPDATE setting SET good_low_temp='$set1', good_hi_temp='$set2', good_low_hum='$set3', good_hi_hum='$set4', danger_hi_temp='$set5', led_start_hour='$set6', led_end_hour='$set7' WHERE id = 1";
        $statement = $db->query($sql);

        $sql = "SELECT * FROM setting WHERE id = 1";
        $statement = $db->query($sql);
    }

    //設定取得
    $sql = "SELECT * FROM setting WHERE id = 1";
    $statement = $db->query($sql);

    while ($row = $statement->fetch()) {
        $rows = $row;
    }
    $db = null;
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
  <title>設定</title>
  <link rel="shortcut icon" href="assets/img/favicon_outikansoku.png">

  <!-- BootstrapのCSS読み込み -->
  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
  <!-- jQuery読み込み -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
  <!-- BootstrapのJS読み込み -->
  <script src="assets/js/bootstrap.min.js"></script>
  <script>
    function doReloadWithCache() {
      // キャッシュを利用してリロード
      window.location.reload(false);
    }
  </script>
</head>

<body>
  <!--ナビゲーションメニュー-->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <a class="navbar-brand" href="./index.php">おうち観測　</a>
      <!--レスポンシブ-->
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="ナビゲーションの切替">
        <span class="navbar-toggler-icon"></span>
      </button>
      <!--メニュー項目-->
      <div class="collapse navbar-collapse" id="navbar">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item">
            <a class="nav-link" href="./index.php">ホーム </a>
          </li>
          <li class="nav-item active">
            <a class="nav-link" href="">設定 <span class="sr-only">(現位置)</span></a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container p-3">
    <p class="font-weight-bold">設定</p>
    <div class="form-group">
      <form action="./setting.php" method="post" class=”form-inline”>
        <div class="border rounded form-group" style="padding:10px;">
          <!-- 室温の設定-->
          <p class="font-weight-bold">室温（℃）</p>
          Low
          <select name="good_low_temp" class="form-control" onchange="submit(this.form)">
            <?php for ($i = 0; $i <= 30; $i++) { ?>
              <option value="<?php echo $i; ?>"
               <?php
               if ($rows['good_low_temp'] == $i) {
                   echo 'selected';
               }?>><?php echo $i; ?></option>
            <?php } ?>
          </select>
          Hi
          <select name="good_hi_temp" class="form-control" onchange="submit(this.form)">
            <?php for ($i = 0; $i <= 30; $i++) { ?>
              <option value="<?php echo $i; ?>"
              <?php
              if ($rows['good_hi_temp'] == $i) {
                  echo 'selected';
              } ?>><?php echo $i; ?></option>
            <?php } ?>
          </select>
        </div>

        <!-- 湿度の設定-->
        <div class="border rounded form-group" style="padding:10px;">
          <p class="font-weight-bold">湿度（%）</p>
          Low
          <select name="good_low_hum" class="form-control" onchange="submit(this.form)">
            <?php for ($i = 20; $i <= 70; $i++) { ?>
              <option value="<?php echo $i; ?>"
              <?php if ($rows['good_low_hum'] == $i) {
                  echo 'selected';
              } ?>><?php echo $i; ?></option>
            <?php } ?>
          </select>
          Hi
          <select name="good_hi_hum" class="form-control" onchange="submit(this.form)">
            <?php for ($i = 20; $i <= 70; $i++) { ?>
              <option value="<?php echo $i; ?>"
              <?php if ($rows['good_hi_hum'] == $i) {
                  echo 'selected';
              } ?>><?php echo $i; ?></option>
            <?php } ?>
          </select>
        </div>

        <!--　危険温度・湿度の設定-->
        <div class="border rounded form-group" style="padding:10px;">
          <p class="font-weight-bold">危険温度（℃）</p>
          <select name="danger_hi_temp" class="form-control" onchange="submit(this.form)">
            <?php for ($i = 20; $i <= 60; $i++) { ?>
              <option value="<?php echo $i; ?>"
               <?php
                if ($rows['danger_hi_temp'] == $i) {
                    echo 'selected';
                }?>>
                <?php echo $i; ?></option>
            <?php } ?>
          </select>
        </div>

        <!-- LED時間の設定-->
        <div class="border rounded form-group" style="padding:10px;">
          <p class="font-weight-bold">LED点灯時間（時）</p>
          Start
          <select name="led_start_hour" class="form-control" onchange="submit(this.form)">
            <?php for ($i = 0; $i <= 24; $i++) { ?>
            <option value="<?php echo $i; ?>" 
            <?php
            if ($rows['led_start_hour'] == $i) {
                echo 'selected';
            } ?>><?php echo $i; ?></option>
            <?php } ?>
          </select>
          End
          <select name="led_end_hour" class="form-control" onchange="submit(this.form)">
            <?php for ($i = 0; $i <= 24; $i++) { ?>
            <option value="<?php echo $i; ?>"
            <?php
            if ($rows['led_end_hour'] == $i) {
                echo 'selected';
            } ?>><?php echo $i; ?></option>
            <?php } ?>
          </select>
        </div>
      </div>
    </form>
  </div>
</body>
</html>
