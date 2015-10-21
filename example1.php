<?php
require_once "vendor/autoload.php";

// A random JS object with a pure function inside.
$person = [
    "name" => "John",
    "mail" => function ($msg) {
        return [
            "author" => "John",
            "message" => $msg,
        ];
    },
];

// A random JS object with a pure function inside.
$personStr = \Kanti\LJSON::stringify($person);
$personVal = \Kanti\LJSON::parse($personStr);
$mailFunction = $personVal->mail;
$mail = $mailFunction("hello");// would crash with JSON

echo $personStr . "\n";
//{"name":"John","mail":(v0) => ({"author":"John","message":v0})}
echo \Kanti\LJSON::stringify($mail) . "\n";
//{"author":"John","message":"hello"}

