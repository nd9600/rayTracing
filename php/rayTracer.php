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

function randomPointInUnitSphere(): Vec3
{
    // rejection method:
    // first, we pick a random point in the unit cube where ​x, y and z all range from -1 to +1
    // if the point is outside the sphere, we reject this point and try again
    $unitSphere = new Vec3(1, 1, 1);
    $p = (new Vec3(random(), random(), random()))->multiplyByConstant(2)->subtract($unitSphere);
    while ($p->squaredLength() >= 1) {
        $p = (new Vec3(random(), random(), random()))->multiplyByConstant(2)->subtract($unitSphere);
    }
    return $p;
}

/**
 * Does diffuse reflection of all the objects in a world, otherwise lerp of white to blue
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
        $hitRecordNormal = $hitRecordOfWhereRayHitTheWorld->normal;
        $hitRecordP = $hitRecordOfWhereRayHitTheWorld->p;

        // to do diffuse reflection (they go in random directions, unlike specular reflection) - we want to project a ray in a random direction from the hitpoint and colour it:
        // pick a random point s from the unit radius sphere that is tangent to the hitpoint p, and send a ray from the hitpoint p to the random point s
        // that sphere has center (​p + N), since N is a unit vector perpendicular to the plane tangent to the hitpoint - that's how the normal is defined!
        // so, to get to s, we need a vector from the center of the unit sphere to s

        // objects will be lighter on the top because the reflect fewer times (so they lose less energy)

        $centerOfUnitSphereTangentToHitpoint = $hitRecordP->add($hitRecordNormal);
        $randomReflectionPoint = $centerOfUnitSphereTangentToHitpoint->add(randomPointInUnitSphere());
        $newRay = new Ray($hitRecordP, $randomReflectionPoint->subtract($hitRecordP));

        // multiply by 0.5 so the objects absorb half the energy on each bounce
        return colour($newRay, $world)->multiplyByConstant(0.5);
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
                $iWithAntiAliasing = $numberOfSamples === 1
                    ? $i
                    : $i + random();
                $jWithAntiAliasing = $numberOfSamples === 1
                    ? $j
                    : $j + random();

                $u = floatval(($iWithAntiAliasing) / $nx);
                $v = floatval(($jWithAntiAliasing) / $ny);

                // colour of the ray is determined by its position
                $ray = $camera->getRay($u, $v);
                $col = $col->add(colour($ray, $world));
            }
            $col = $col->divideByConstant($numberOfSamples);

            // it should be light grey, but image viewers assume that the image is “gamma corrected” (meaning the 0 to 1 values have some transform before being stored as a byte)
            // so we have to correct for that: we can use “gamma 2” which means raising the color to the power 1/gamma, or 1/2 in the simple case
            $col = new Vec3(sqrt($col->r()), sqrt($col->g()), sqrt($col->b()));

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