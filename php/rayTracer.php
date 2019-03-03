<?php
declare(strict_types=1);

use RayTracer\Camera;
use RayTracer\Hitable;
use RayTracer\HitableList;
use RayTracer\HitRecord;
use RayTracer\Ray;
use RayTracer\Sphere;
use RayTracer\Vec3;

require "vendor/autoload.php";

# php rayTracer.php > 1.ppm
# psysh rayTracer.php | tail -n +2 | bat

/**
 * Returns a random float 0 <= x < 1
 * @return float
 */
function random(): float
{
    return mt_rand(0, mt_getrandmax() - 1) / mt_getrandmax();
}

/**
 * Colours the surface normals of all objects in a world, otherwise lerp of white to blue
 * @param Ray $ray
 * @param Hitable $world
 * @return Vec3
 */
function colour(Ray $ray, Hitable $world): Vec3
{
    /** @var boolean $didRayHitTheWorld */
    /** @var HitRecord $hitRecordOfWhereRayHitTheWorld */
    [$didRayHitTheWorld, $hitRecordOfWhereRayHitTheWorld] = $world->hit($ray, 0, INF);
    if ($didRayHitTheWorld) {
        $surfaceNormal = $hitRecordOfWhereRayHitTheWorld->normal;

        // N is a unit length vector - so each component is between -1 and 1, then we map each component to the interval from 0 to 1, and then map x/y/z to r/g/b
        $normalAsColourMap = (new Vec3($surfaceNormal->x() + 1, $surfaceNormal->y() + 1, $surfaceNormal->z() + 1))->multiplyByConstant(0.5);
        return $normalAsColourMap;
    }

    $unitDirection =  $ray->direction()->makeUnitVector();
    $t = 0.5 * ($unitDirection->y() + 1);
    
    # linear interpolation / lerp of white to blue
    $r = (1 - $t) + ($t * 0.5);
    $g = (1 - $t) + ($t * 0.7);
    $b = (1 - $t) + ($t * 1.0);
    return new Vec3($r, $g, $b);
}

/**
 * @param resource $file
 * @param int $scale
 */
function writeFile($file, int $scale = 1)
{
    $nx = 200 * $scale;
    $ny = 100 * $scale;
    $numberOfSamples = 1;

    fwrite($file, "P3\n");
    fwrite($file, "{$nx} {$ny}\n");
    fwrite($file, "255\n");

    $camera = new Camera();

    $listOfHitables = [
        new Sphere(new Vec3(0, 0, -1), 0.5),
        new Sphere(new Vec3(0, -100.5, -1), 100)
    ];
    $world = new HitableList($listOfHitables);

    for ($j = $ny - 1; $j >= 0; $j--) {
        for ($i = 0; $i < $nx; $i++) {

            $col = new Vec3(0, 0, 0);
            for ($s = 0; $s < $numberOfSamples; $s++) {
                $u = floatval(($i + random()) / $nx);
                $v = floatval(($j + random()) / $ny);

                // colour of the ray is determined by its position
                $ray = $camera->getRay($u, $v);
                $col = $col->add(colour($ray, $world));
            }
            $col = $col->divideByConstant($numberOfSamples);

            $ir = intval(255.99 * $col[0]);
            $ig = intval(255.99 * $col[1]);
            $ib = intval(255.99 * $col[2]);

            fwrite($file, "{$ir} {$ig} {$ib}\n");
        }
    }
}

$file = fopen("output.ppm", "w") or die("Unable to open file!");
writeFile($file, 1);
fclose($file);

exit();