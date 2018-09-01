<?php

// Composerでインストールしたライブラリを一括読み込み
require_once __DIR__ . '/vendor/autoload.php';
//DBクラスを使うためにindex.phpを読み込む
require_once('index.php');
require_once('MyValidator.php');

// アクセストークンを使いCurlHTTPClientをインスタンス化
$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(getenv('CHANNEL_ACCESS_TOKEN'));
// CurlHTTPClientとシークレットを使いLINEBotをインスタンス化
$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => getenv('CHANNEL_SECRET')]);
$v = new MyValidator();

//$message　nameのパラーメーターをメッセージとする
$message = htmlspecialchars($_GET["name"]);
$v->lengthCheck($message,'length',23);
//$v->regexCheck($message,'message','/(Low|High)--(USDJPY|EURJPY|GBPJPY|AUDJPY|NZDJPY|EURUSD|AUDUSD)--[0-9]{1,3}\.[0-9]{5}/');
$v();

echo 'stage1';

$ids = getUserIds();

if($ids === PDO::PARAM_NULL){
  error_log('There is no id');
}

// メッセージをユーザーID宛にプッシュ
//$response = $bot->pushMessage($ids, new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message));

// メッセージを複数人にプッシュ
$response = $bot->multicast($ids, new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($message));

if (!$response->isSucceeded()) {
  error_log('Failed!'. $response->getHTTPStatus . ' ' . $response->getRawBody());
}

// ユーザーIDをデータベースから取得
function getUserIds() {
  //DBクラスを使うためにindex.phpを読み込む
  require_once('index.php');

  $dbh = dbConnection::getConnection();
//  $sql = 'select userid from ' . TABLE_NAME_STONES . ' where ? = pgp_sym_decrypt(userid, \'' . getenv('DB_ENCRYPT_PASS') . '\')';

  $sql = 'select pgp_sym_decrypt(userid,\''. getenv('DB_ENCRYPT_PASS') .'\') from mt4testj';
  $sth = $dbh->prepare($sql);
  $sth->execute();

  //fetchAll(PDO::FETCH_COLUMN, 0); 0列目の要素を全て配列で取得

  // レコードが存在しなければNULL
  if (!($ids=$sth->fetchAll(PDO::FETCH_COLUMN, 0))) {
    return PDO::PARAM_NULL;
  }

  return $ids;
}
?>
