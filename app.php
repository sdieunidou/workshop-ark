<?php

use App\Ark\ArkWorkshop;

require 'vendor/autoload.php';

$workshop = new ArkWorkshop();
$types = $workshop->getTypes();

foreach ($types as $type) {
    $items = $workshop->get($type['slug']);
    echo sprintf('%d items in type %s (%s)', count($items), $type['label'], $workshop->getBrowseUrl($type['slug'])) . "\n";
}
