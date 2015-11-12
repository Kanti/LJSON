<?php
require_once "../vendor/autoload.php";

/***************
 * parseWithStdLib()
 * simple example
 **************/
echo 'parseWithStdLib() simple:' . PHP_EOL;

$func = function ($lib, $one, $tow) {
    return [$one, $lib('*', $tow, 2)];
};

$ljsonString = \Kanti\LJSON::stringify($func);
echo PHP_EOL . $ljsonString . PHP_EOL . PHP_EOL; //(v0,v1,v2) => ([v1,v0('*',v2,2)])

$functionFromClient = \Kanti\LJSON::parseWithStdLib($ljsonString);
echo json_encode($functionFromClient(2, 3)) . PHP_EOL;

/***************
 * parseWithStdLib()
 * complex example
 **************/
echo 'parseWithStdLib() complex:' . PHP_EOL;

$func = function ($lib, $one, $tow) {
    return function () use ($lib, $one, $tow) {
        return [$one, $lib('*', $tow, 2)];
    };
};

$ljsonString = \Kanti\LJSON::stringify($func);
echo PHP_EOL . $ljsonString . PHP_EOL . PHP_EOL; //(v0,v1,v2) => ([v1,v0('*',v2,2)])

$functionFromClient = \Kanti\LJSON::parseWithStdLib($ljsonString);
$resultFunction1 = $functionFromClient(2, 3);
echo json_encode($resultFunction1()) . PHP_EOL;

/***************
 * withStdLib()
 * simple example
 **************/
echo 'withStdLib() simple:' . PHP_EOL;

$func = function ($lib, $one, $tow) {
    return [$one, $lib('*', $tow, 2)];
};

$resultFunction1 = \Kanti\LJSON::withStdLib($func);
echo json_encode($resultFunction1(2, 3)) . PHP_EOL;
