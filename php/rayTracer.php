<?php
declare(strict_types=1);

use RayTracer\RayTracer;

require "vendor/autoload.php";

# it'll hit xdebug's maximum nesting level of 256 otherwise
ini_set('xdebug.max_nesting_level', '1000');

# php rayTracer.php > 1.ppm
# psysh rayTracer.php | tail -n +2 | bat

$rayTracer = new RayTracer();

$file = fopen("output.ppm", "w") or die("Unable to open file!");
$rayTracer->writeFile($file, 3, 10);
fclose($file);

exit();
