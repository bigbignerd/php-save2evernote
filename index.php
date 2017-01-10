<?php
require 'EvernoteModel.php';
require 'config.php';

$model = new EvernoteModel($config);
//接收命令行参数
unset($argv[0]);
$content = implode(" ", $argv);

$model->add($content);

?>