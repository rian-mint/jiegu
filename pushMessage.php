<?php

//DBクラスを使うためにindex.phpを読み込む
require_once('index.php');
//require_once('MyValidator.php');
define('TABLE_NAME', 'ids');
//$v = new MyValidator();

//$accountNo　nameのパラーメーターをメッセージとする
$accountNo = htmlspecialchars($_GET["accountNo"]);
//$v->lengthCheck($accountNo,'length',23);
//$v->regexCheck($accountNo,'message','/(Low|High)--(USDJPY|EURJPY|GBPJPY|AUDJPY|NZDJPY|EURUSD|AUDUSD)--[0-9]{1,3}\.[0-9]{5}/');
//$v();

$isRegisterd = false;
$ids = getUserIds();

if($ids === PDO::PARAM_NULL){
  error_log('There is no id');
}

//アカウント番号が登録されているかチェックする
foreach ($ids as $id) {
  if( (string)$id === $accountNo)
  {
    $isRegisterd = true;
    break;
  }
}

if ($isRegisterd) echo 'true';
else echo 'false';
?>
