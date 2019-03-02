<?php
declare(strict_types=1);

use RayTracer\Ray;
use RayTracer\Vec3;

require "vendor/autoload.php";

# php rayTracer.php > 1.ppm
# psysh rayTracer.php | tail -n +2 | bat

/**
 * Linearly blends white and blue depending on the up/downess of the y coordinate.
 * I first made it a unit vector so -1.0 < ​ y ​ < 1.0. I then did a standard graphics trick of scaling that to 0.0 < t < 1.0.
 * When t=1.0 I want blue. When t = 0.0 I want white. In between, I want a blend.
 * @param Ray $ray
 * @return Vec3
 */
function colour(Ray $ray): Vec3
{
    $unitDirection =  $ray->direction()->makeUnitVector();
    $t = 0.5 * ($unitDirection->y() + 1);
    
    # linear interpolation / lerp of white to blue
    $e0 = (1 - $t) + ($t * 0.5);
    $e1 = (1 - $t) + ($t * 0.7);
    $e2 = (1 - $t) + ($t * 1.0);
    return new Vec3($e0, $e1, $e2);
}

/**
 * @param resource $file
 * @param int $scale
 */
function writeFile($file, int $scale = 1)
{
    $nx = 200 * $scale;
    $ny = 100 * $scale;
    fwrite($file, "P3\n");
    fwrite($file, "{$nx} {$ny}\n");
    fwrite($file, "255\n");

    $lowerLeftCorner = new Vec3(-2, -1, -1);
    $horizontal = new Vec3(4, 0, 0);
    $vertical = new Vec3(0, 2, 0);
    $origin = new Vec3(0, 0, 0);

    for ($j = $ny - 1; $j >= 0; $j--) {
        for ($i = 0; $i < $nx; $i++) {

            $u = floatval($i / $nx);
            $v = floatval($j / $ny);

            // direction = lowerLeftCorner + u*horizontal + v*vertical
            $direction = $lowerLeftCorner
                ->add($horizontal->multiplyByConstant($u))
                ->add($vertical->multiplyByConstant($v));
            $ray = new Ray($origin, $direction);

            // colour of the ray is determined by its position
            $col = colour($ray);
            $ir = intval(255.99 * $col[0]);
            $ig = intval(255.99 * $col[1]);
            $ib = intval(255.99 * $col[2]);

            fwrite($file, "{$ir} {$ig} {$ib}\n");
        }
    }
}

$file = fopen("output.ppm", "w") or die("Unable to open file!");
writeFile($file);
fclose($file);

exit();