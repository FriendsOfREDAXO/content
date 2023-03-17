<?php

$host = '127.0.0.1';
$user = 'root';
$password = 'root';
$dbname = 'redaxo5';
$sql = null;

$rexstanLevel = 9;
$rexstanExtensions = [
    realpath(__DIR__ . '/../../rexstan/config/rex-superglobals.neon'),
    realpath(__DIR__ . '/../../rexstan/vendor/phpstan/phpstan/conf/bleedingEdge.neon'),
    realpath(__DIR__ . '/../../rexstan/vendor/phpstan/phpstan-strict-rules/rules.neon'),
    realpath(__DIR__ . '/../../rexstan/vendor/phpstan/phpstan-deprecation-rules/rules.neon'),
    realpath(__DIR__ . '/../../rexstan/config/phpstan-phpunit.neon'),
    realpath(__DIR__ . '/../../rexstan/config/phpstan-dba.neon'),
    realpath(__DIR__ . '/../../rexstan/config/cognitive-complexity.neon'),
    realpath(__DIR__ . '/../../rexstan/config/code-complexity.neon'),
    realpath(__DIR__ . '/../../rexstan/config/dead-code.neon')
];

try {
    $connection = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "INSERT INTO rex_config (namespace, key, value) VALUES ('rexstan', 'addons', '"|' . realpath(__DIR__ . "/../") . '|"')";
    $sql = "INSERT INTO rex_config (namespace, key, value) VALUES ('rexstan', 'extensions', '"|' . implode('|', $rexstanExtensions) . '|"')";
    $sql = "INSERT INTO rex_config (namespace, key, value) VALUES ('rexstan', 'level', '" . $rexstanLevel . "')";
    $sql = "INSERT INTO rex_config (namespace, key, value) VALUES ('rexstan', 'phpversion', '80109')";
    // use exec() because no results are returned
    $connection->exec($sql);
    echo "New record created successfully";
}
catch (PDOException $e) {
    echo $sql . "<br>" . $e->getMessage();
}