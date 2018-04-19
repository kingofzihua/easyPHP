<?php
//require_once './src/index.php';
//$res = \Core\Dispatcher::getInstance()->fastRouter('/kingofhua/php','GET');
//
//var_dump($res);
$sweet = array('a' => 'apple', 'b' => 'banana');
echo '<pre>';

function test_print(&$item, $key)
{
    $item = $item . '123';
}


array_walk_recursive($sweet, 'test_print');