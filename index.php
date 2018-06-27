<?php

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
// ユーザーIDをデータベースから取得
function getUserIds() {

  $dbh = dbConnection::getConnection();
  $sql = 'select id from ids';
  $sth = $dbh->prepare($sql);
  $sth->execute();

  // レコードが存在しなければNULL
  if (!($ids=$sth->fetchAll(PDO::FETCH_COLUMN, 0))) {
    return PDO::PARAM_NULL;
  }

  return $ids;
}
?>
