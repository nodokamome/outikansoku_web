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

    $sql = 'SELECT * FROM setting WHERE id = 1';
    $statement = $db->query($sql);

    while ($row = $statement->fetch()) {
        $rows = $row;
    }
    echo $rows['led_end_hour'];
    $db = null;
} catch (PDOException $e) {
    echo $e->getMessage();
    exit;
}
