<?php

// Composerでインストールしたライブラリを一括読み込み
//require_once __DIR__ . '/vendor/autoload.php';
//DBクラスを使うためにindex.phpを読み込む
require_once('index.php');
require_once('MyValidator.php');

$v = new MyValidator();
echo '1';
//$accountNo　nameのパラーメーターをメッセージとする
$accountNo = htmlspecialchars($_GET["accountNo"]);
$v->lengthCheck($accountNo,'length',23);
//$v->regexCheck($accountNo,'message','/(Low|High)--(USDJPY|EURJPY|GBPJPY|AUDJPY|NZDJPY|EURUSD|AUDUSD)--[0-9]{1,3}\.[0-9]{5}/');
$v();

$ids = getUserIds();

if($ids === PDO::PARAM_NULL){
  error_log('There is no id');
}
echo '2';

$isRegisterd = false;
// メッセージをユーザーID宛にプッシュ
foreach ($ids as $id) {
  if($id === $accountNo)
  {
    $isRegisterd = true;
    break;
  }
}
echo '3';

if ($isRegisterd)
{
 echo 'true';
}
else
{
  echo 'false';
}

// ユーザーIDをデータベースから取得
function getUserIds() {
  //DBクラスを使うためにindex.phpを読み込む
  require_once('index.php');
  $dbh = dbConnection::getConnection();
  $sql = 'select userid from ids';
  $sth = $dbh->prepare($sql);
  $sth->execute();

  // レコードが存在しなければNULL
  if (!($ids=$sth->fetchAll(PDO::FETCH_COLUMN, 0))) {
    return PDO::PARAM_NULL;
  }

  return $ids;
}
?>
