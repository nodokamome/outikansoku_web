<?php
//データベースから情報取得
require("../../../../config.php");
define('DB_DATABASE', $DB_DATABASE);
define('DB_USERNAME', $DB_USERNAME);
define('DB_PASSWORD', $DB_PASSWORD);
define('PDO_DSN', 'mysql:host=localhost;dbname=' . DB_DATABASE);
try {
    //DB接続
    $db = new PDO(PDO_DSN, DB_USERNAME, DB_PASSWORD);
    //エラーをスロー
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = 'SELECT * FROM token WHERE id = 1';
    $statement = $db->query($sql);

    while ($row = $statement->fetch()) {
        $rows = $row;
    }
    $token = $rows['token'];
    $json = json_decode(file_get_contents('php://input'), true);
    if (isset($json['bot'])) {
        $sql = 'SELECT * FROM sensor ORDER BY datetime DESC LIMIT 1';
        $statement = $db->query($sql);

        //レコード件数取得
        $row_count = $statement->rowCount();

        while ($row = $statement->fetch()) {
            $rows = $row;
        }

        $datetime = $rows["datetime"];
        $temp = $rows["temp"];
        $hum = $rows["hum"];

        room_checker($datetime, $temp, $hum, "bot");
        //データベース接続切断
        $db = null;
    } elseif (isset($json['datetime']) && $token == $json['token']) {
        $datetime = $json["datetime"];
        $temp = $json["temp"];
        $hum = $json["hum"];

        //データインサート
        $sql = "INSERT INTO sensor(datetime,temp,hum) VALUES('$datetime','$temp','$hum')";
        $statement = $db->query($sql);

        room_checker($datetime, $temp, $hum, "raspi");
        //データベース切断
        $db = null;
    }
} catch (PDOException $e) {
    echo $e->getMessage();
    exit;
}

//部屋状態の取得
function room_checker($datetime, $temp, $hum, $name)
{
    //DB接続
    $db = new PDO(PDO_DSN, DB_USERNAME, DB_PASSWORD);
    //エラーをスロー
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //設定を取得し、判断材料として保存
    $sql = "SELECT * FROM setting where id=1";
    $statement = $db->query($sql);
    while ($row = $statement->fetch()) {
        $good_low_temp = $row["good_low_temp"];
        $good_hi_temp = $row["good_hi_temp"];
        $good_low_hum = $row["good_low_hum"];
        $good_hi_hum = $row["good_hi_hum"];
        $danger_hi_temp = $row["danger_hi_temp"];
    }

    //室温、湿度から状態を判断
    if ($temp > $danger_hi_temp) {
        $result = "室温が高すぎます！！火事かもしれません！！！";
    } elseif ($good_hi_temp <= $temp && $temp <= $danger_hi_temp) {
        $result = "先輩、ちょっと暑くないですか？頭がぼーっとします～";
    } elseif ($good_low_temp <= $temp && $temp <= $good_hi_temp && $good_low_hum <= $hum && $hum <= $good_hi_hum && $temp <= $danger_hi_temp) {
        $result = "先輩！お部屋が最高の状態です！快適で仕事がはかどります！！";
    } elseif ($temp < $good_low_temp) {
        $result = "先輩、寒くないですか？手がかじかんでタイピングできないです～";
    } elseif ($good_low_temp <= $temp && $temp <= $good_hi_temp && $hum < $good_low_hum) {
        $result = "お部屋が乾燥してます～、ウィルス対策のためにも加湿しませんか？";
    } elseif ($good_low_temp <= $temp && $temp <= $good_hi_temp && $hum < $good_low_hum) {
        $result = "すごくじめじめしません？、カビが生えるかもしれないです～";
    } elseif ($good_hi_temp < $temp && $good_hi_hum < $hum) {
        $result = "暑いし、じめじめして、この部屋最悪です。。とりあえずエアコンつけませんか先輩？";
    }

    //ラズパイからアクセス時と、Botからアクセス時で送信内容を選択
    if ($name == "raspi") {
        echo $result;
    } elseif ($name == "bot") {
        $ary = [
            'result' => $result,
            'datetime' => $datetime,
            'temp' => $temp,
            'hum' => $hum,
        ];
        $json = json_encode($ary, JSON_UNESCAPED_UNICODE);
        echo $json;
    }

    //データベース切断
    $db = null;
}
