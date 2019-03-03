<?php
declare(strict_types=1);

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
 * Colours the surface normals of a small sphere we place at -1 on the z-axis, otherwise lerp of white to blue
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
    fwrite($file, "P3\n");
    fwrite($file, "{$nx} {$ny}\n");
    fwrite($file, "255\n");

    $lowerLeftCorner = new Vec3(-2, -1, -1);
    $horizontal = new Vec3(4, 0, 0);
    $vertical = new Vec3(0, 2, 0);
    $origin = new Vec3(0, 0, 0);

    $listOfHitables = [
        new Sphere(new Vec3(0, 0, -1), 0.5),
//        new Sphere(new Vec3(0, -100.5, -1), 100)
    ];
    $world = new HitableList($listOfHitables);

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
            $col = colour($ray, $world);
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