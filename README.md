[![Travis](https://img.shields.io/travis/Kanti/LJSON.svg?style=flat-square)](https://travis-ci.org/Kanti/LJSON/)
[![Packagist](https://img.shields.io/packagist/l/kanti/ljson.svg?style=flat-square)](https://www.gnu.org/licenses/gpl-2.0.html)
[![Code Climate](https://img.shields.io/codeclimate/github/Kanti/LJSON.svg?style=flat-square)](https://codeclimate.com/github/Kanti/LJSON)
[![Code Climate](https://img.shields.io/codeclimate/coverage/github/Kanti/LJSON.svg?style=flat-square)](https://codeclimate.com/github/Kanti/LJSON/coverage)
# LJSON

LJSON is a drop-in replacement for [JSON](http://www.json.org) which also allows you to parse and stringify pure functions and their contents. There are good security reasons for functions to be out of the JSON specs, but most of those are only significant when you allow arbitrary, side-effective programs. With pure functions, one is able to interchange code while still being as safe as with regular JSON.

> note: <br> this is a port of [LJSON for JavaScript](https://github.com/MaiaVictor/LJSON) originaly from [MaiaVictor](https://github.com/MaiaVictor)

````php
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
$personStr    = \Kanti\LJSON::stringify($person);
$personVal    = \Kanti\LJSON::parse($personStr);
$mailFunction = $personVal->mail;
$mail         = $mailFunction("hello");// would crash with JSON

echo $personStr . "\n";
echo \Kanti\LJSON::stringify($mail) . "\n";
````

### output:
````js
{"name":"John","mail":(v0) => ({"author":"John","message":v0})}
{"author":"John","message":"hello"}
````

## More info:
- [Installing](#installing)
- <a href="https://github.com/MaiaVictor/LJSON#why" target="_blank">Why?</a>⇗
- <a href="https://github.com/MaiaVictor/LJSON#using-primitives" target="_blank">Using primitives</a>⇗
- <a href="https://github.com/MaiaVictor/LJSON#safety" target="_blank">Safety</a>⇗
- [TODO](#todo)

## Installing

````batch
composer require kanti/ljson
````

## TODO
 - Library support
 - standard Library
