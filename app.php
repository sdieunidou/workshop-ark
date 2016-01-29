<?php

use App\Ark\ArkWorkshop;

require 'vendor/autoload.php';

$types = (new ArkWorkshop())->getTypes();
var_dump($types);
