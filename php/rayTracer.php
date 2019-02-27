<?php
declare(strict_types=1);

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
        
        $ir = intval(255.99 * $r);
        $ig = intval(255.99 * $g);
        $ib = intval(255.99 * $b);
        
        echo "{$ir} {$ig} {$ib}\n";
    }
}

exit();