<?php
require_once "../vendor/autoload.php";

/*
 * Client:
 */
$func = function () {
    echo "Hallo";
    return "World";
};

$ljsonString = \Kanti\LJSON::stringify($func);
//Hello

echo "\nsend to server -> " . $ljsonString . "\n";
//<br>
// send to server -> () => ("World")
//<br>

/*
 * Server:
 */
$functionFromClient = \Kanti\LJSON::parse($ljsonString);
echo $functionFromClient();
//World


/*
 * The full Result:
 */
$fullResult =
'Hallo
send to server -> () => ("World")
World';
