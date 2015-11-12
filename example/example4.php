<?php
require_once "../vendor/autoload.php";

/***************
 * stringify()
 * Wrong:
 **************/
echo 'stringify() wrong:' . PHP_EOL;

$func = function ($first) {
    return $first + 3;
};

$ljsonString = \Kanti\LJSON::stringify($func);
//Notice: Object of class Kanti\Parameter could not be converted to int in D:\www\LJSON\example\example4.php on line 11
echo PHP_EOL . $ljsonString . PHP_EOL . PHP_EOL; //(v0) => (4)

$functionFromClient = \Kanti\LJSON::parse($ljsonString);
echo json_encode($functionFromClient(7)) . PHP_EOL;
//4
echo json_encode($functionFromClient(9999)) . PHP_EOL;
//4

/***************
 * stringify()
 * Right:
 **************/
echo 'stringify() right' . PHP_EOL;

$func = function ($stdLib, $first) {
    return $stdLib('+', $first, 3);
};

$ljsonString = \Kanti\LJSON::stringify($func);
//Notice: Object of class Kanti\Parameter could not be converted to int in D:\www\LJSON\example\example4.php on line 11
echo PHP_EOL . $ljsonString . PHP_EOL . PHP_EOL; //(v0,v1) => (v0("+",v1,1))

$functionFromClient = \Kanti\LJSON::parseWithStdLib($ljsonString);
echo json_encode($functionFromClient(7)) . PHP_EOL;
//10
echo json_encode($functionFromClient(9999)) . PHP_EOL;
//10002
