<?php

//DBクラスを使うためにindex.phpを読み込む
require_once('index.php');
require_once('MyValidator.php');
define('TABLE_NAME', 'ids');
$v = new MyValidator();

//$accountNoをパラーメーターで指定する
$accountNo = htmlspecialchars($_GET["accountNo"]);
//$v->lengthCheck($accountNo,'length',20);
//$v->regexCheck($accountNo,'message','/[0-9]+/');
$v();

echo $accountNo ;

$isRegisterd = false;
$ids = getUserIds();

if($ids === PDO::PARAM_NULL){
  error_log('There is no id');
  echo 'There is no id';
}

//アカウント番号が登録されているかチェックする
foreach ($ids as $id) {
  if((string)$id === $accountNo)
  {
    $isRegisterd = true;
    break;
  }
}

if ($isRegisterd) echo 'true';
else echo 'false';
?>
