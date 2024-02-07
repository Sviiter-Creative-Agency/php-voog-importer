<?php

use Voog\Parser;
use Voog\VoogApi;

require 'config.php';
require 'vendor/autoload.php';

$json = file_get_contents('data.json');
$json = json_decode($json, true);

try {

    $migrate = new \Voog\Migrate($json['data'][$_SERVER['argv'][1]]);
    $result = $migrate->doMigrate();

    printf("Article #{$result->id} imported successfully! ({$migrate->getParser()->getTitle()})");

} catch (Exception $e) {
    printf('Error: ' . $e->getMessage());
}

echo PHP_EOL;
exit;