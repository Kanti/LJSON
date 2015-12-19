<?php
require_once "../vendor/autoload.php";

// A random object with a pure function inside.
$person = [
    "name" => "John",
    "mail" => function ($msg) {
        return [
            "author" => "John",
            "message" => $msg,
        ];
    },
];

$personStr = \LJSON\LJSON::stringify($person);
$personVal = \LJSON\LJSON::parse($personStr);
$mailFunction = $personVal->mail;
$mail = $mailFunction("hello");// would crash with JSON

echo $personStr . "\n";
//{"name":"John","mail":(v0) => ({"author":"John","message":v0})}
echo \LJSON\LJSON::stringify($mail) . "\n";
//{"author":"John","message":"hello"}
