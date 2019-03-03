<?php
declare(strict_types=1);

use RayTracer\Ray;
use RayTracer\Vec3;

require "vendor/autoload.php";

# php rayTracer.php > 1.ppm
# psysh rayTracer.php | tail -n +2 | bat

/**
 * Returns the value of t in $r = A + tB where a ray $r hits a sphere centered at $center with a radius $radius, or -1 if it doesn't hit (you can sub-in t later to get the exact position)
 * @param Vec3 $center
 * @param float $radius
 * @param Ray $r
 * @return float
 */
function whereRayHitsSphere(Vec3 $center, float $radius, Ray $r): float
{
    // at the origin, the equation of a sphere is x^2 + y^2 = r^2
    // centered at point c, is (x-c)^2 + (y-c)^2 = r^2

    // vector from c = (c_x, c_y, c_z) to P = (x,y,z) is (P - c) = (x - c_x, y - c_y, z - c_z)
    // (P - c) dot (P - c) = ((x - c_x)^2 + (y - c_y)^2 + (z - c_z)^2) * cos theta
    // theta = 0 so cos theta = 1, so
    // (P - c) dot (P - c) = (x - c_x)^2 + (y - c_y)^2 + (z - c_z)^2 = equation of a sphere centered at c
    // put (P - c) dot (P - c) equal to r^2 and you have the equation of a sphere centered at c with radius r
    // (P - c) dot (P - c) = r^2 means any point p that satisfies this equation is on the sphere

    // P only has one parameter, t (we set A = the camera and B = the direction vector)
    // we want to know for what t (if any) does P hit the sphere i.e.
    // (P(t) - c) dot (P(t) - c) = r^2
    // (A + t*B - C) dot (A + t*B - C) = r^2

    // expanding and moving all terms to the left, we get
    // (t^2 * B dot B) + (2t * B dot (A - C)) + ((A - C) dot (A - C)) - r^2 = 0
    // we know A, B, C and r, so we can solve this quadratic equation for t

    // quadratic formula
    // x  = ( -b +- sqrt(b^2 - 4ac) )/ 2a
    // x1 = ( -b + sqrt(b^2 - 4ac) )/ 2a
    // x2 = ( -b - sqrt(b^2 - 4ac) )/ 2a

    // you can't have the sqrt of a negative number, so if b^2 - 4ac is negative, there are roots,
    // if b^2 - 4ac = 0, sqrt(0) = 0, so x1 = x2 = ( -b +- 0 )/ 2a = -b / 2a
    // if b^2 - 4ac is positive, there are 2 roots

    // if it has no roots, it doesn't hit the sphere (discriminant is -ve)
    // if 1 root, tangent to the sphere (discriminant is 0)
    // if 2 roots, goes through the sphere (discriminant is +ve)

    // we can test it by colouring red any pixel that hits a small sphere we place at -1 on the z-axis

    // (t^2 * B dot B) + (2t * B dot (A - C)) + ((A - C) dot (A - C)) - r^2 = 0
    // a                 b                             c
    // a = B dot B       b = 2 * B dot (A - C)         c = (A - C) dot (A - C) - r^2
    // a vector X - Y means the vector *from* Y to X:
    // since 0 + Y gets you to Y, then X - Y gets you to X
    // 0 + Y + (X - Y) = X
    // so A - C is from the center of the sphere to the ray's origin (i.e. the camera)

    /** @var Vec3 A - C */
    $rOrigin = $r->origin();
    $fromCenterToRayOrigin = $rOrigin->subtract($center);

    $rDirection = $r->direction();
    $a = $rDirection->dot($rDirection);
    $b = 2 * $fromCenterToRayOrigin->dot($rDirection);
    $c = $fromCenterToRayOrigin->dot($fromCenterToRayOrigin) - $radius**2;

    $discriminant = $b**2 - 4*$a*$c;
    if ($discriminant < 0) {
        return -1;
    }
    return (-$b - sqrt($discriminant)) / (2 * $a);
}

/**
 * Colours the surface normals of a small sphere we place at -1 on the z-axis, otherwise lerp of white to blue
 * @param Ray $ray
 * @return Vec3
 */
function colour(Ray $ray): Vec3
{
    $sphereCenter = new Vec3(0, 0, -1);
    $sphereRadius = 0.5;
    $valueOfTWhereRayHitsSphere = whereRayHitsSphere($sphereCenter, $sphereRadius, $ray);
    $rayHitsSphere = $valueOfTWhereRayHitsSphere > 0;
    if ($rayHitsSphere) {
        $whereRayHitsSphere = $ray->pointAtParameter($valueOfTWhereRayHitsSphere);

        // N is a unit length vector - so each component is between -1 and 1, then we map each component to the interval from 0 to 1, and then map x/y/z to r/g/b
        //r/g/b
        $surfaceNormal = $whereRayHitsSphere->subtract($sphereCenter)->makeUnitVector();
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
writeFile($file, 1);
fclose($file);

exit();