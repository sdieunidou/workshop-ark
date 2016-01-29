<?php

use App\Ark\ArkWorkshop;

require 'vendor/autoload.php';

$workshop = new ArkWorkshop();
$types = $workshop->getTypes();
if (count($types)) {
    $items = $workshop->get($types[0]['id']);
    var_dump($items);
}
