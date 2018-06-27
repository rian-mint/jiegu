<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="sample.css" type="text/css">
  </head>
  <body>
    great app
  </body>
</html>

<?php

// Composerでインストールしたライブラリを一括読み込み
//require_once __DIR__ . '/vendor/autoload.php';
// テーブル名を定義
define('TABLE_NAME_STONES', 'ids');
// アクセストークンを使いCurlHTTPClientをインスタンス化
$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(getenv('CHANNEL_ACCESS_TOKEN'));

// ユーザーIDを元にデータベースから情報を取得
function getStonesByUserId($userId) {
  $dbh = dbConnection::getConnection();
  $sql = 'select userid from ' . TABLE_NAME_STONES . ' where ? = pgp_sym_decrypt(userid, \'' . getenv('DB_ENCRYPT_PASS') . '\')';
  $sth = $dbh->prepare($sql);
  $sth->execute(array($userId));
  // レコードが存在しなければNULL
  if (!($row = $sth->fetch())) {
    return PDO::PARAM_NULL;
  } else {
    // 石の配置を連想配列に変換し返す
    return true;
  }
}

// データベースへの接続を管理するクラス
class dbConnection {
  // インスタンス
  protected static $db;
  // コンストラクタ
  private function __construct() {

    try {
      // 環境変数からデータベースへの接続情報を取得し
      $url = parse_url(getenv('DATABASE_URL'));
      // データソース
      $dsn = sprintf('pgsql:host=%s;dbname=%s', $url['host'], substr($url['path'], 1));
      // 接続を確立
      self::$db = new PDO($dsn, $url['user'], $url['pass']);
      // エラー時例外を投げるように設定
      self::$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
    }
    catch (PDOException $e) {
      error_log('Connection Error: ' . $e->getMessage());
    }
  }

  // シングルトン。存在しない場合のみインスタンス化
  public static function getConnection() {
    if (!self::$db) {
      new dbConnection();
    }
    return self::$db;
  }
}

?>
