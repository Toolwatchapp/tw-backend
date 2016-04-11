<?php
// Here you can initialize variables that will be available to your tests

$url                       = parse_url(getenv("TW_DB_URL"));
// $db['default']['hostname'] = $url["host"];
// $db['default']['username'] = $url["user"];
// $db['default']['password'] = $url["pass"];
// $db['default']['database'] = substr($url["path"], 1);
//
var_dump($url);

\Codeception\Configuration::$defaultSuiteSettings['modules']['config'] = [
    'Db' => [
        'dsn' => 'mysql:host=' . $url["host"] . ';dbname=tw_ci' . substr($url["path"], 1),
        'user' =>  $url["user"],
        'password' => $url["pass"],
        'dump' => '_data/dump.sql',
        'populate' => true,
        'cleanup' => false,
        'reconnect' => true
    ]
];

var_dump(\Codeception\Configuration::$defaultSuiteSettings);
