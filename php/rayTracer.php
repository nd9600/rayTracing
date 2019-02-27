<?php
declare(strict_types=1);

use RayTracer\Vec3;

require "vendor/autoload.php";

# php rayTracer.php > 1.ppm
# psysh rayTracer.php | tail -n +2 | bat

$nx = 200;
$ny = 100;
echo "P3\n";
echo "{$nx} {$ny}\n";
echo "255\n";

for ($j = $ny - 1; $j >= 0; $j--) {
    for ($i = 0; $i < $nx; $i++) {
        $r = floatval($i / $nx);
        $g = floatval($j / $ny);
        $b = 0.2;
        $vec = new Vec3($r, $g, $b);
        
        $ir = intval(255.99 * $vec[0]);
        $ig = intval(255.99 * $vec[1]);
        $ib = intval(255.99 * $vec[2]);
        
        echo "{$ir} {$ig} {$ib}\n";
    }
}

exit();