<?php
declare(strict_types=1);

namespace RayTracer;


class Sphere extends Hitable
{
    /** @var Vec3 */
    private $center;

    /**  @var float */
    private $radius;

    public function __construct(Vec3 $center, float $radius )
    {
        $this->center = $center;
        $this->radius = $radius;
    }

    public function hit(Ray $ray, float $tMin, float $tMax): array
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

        // (t^2 * B dot B) + (2t * B dot (A - C)) + ((A - C) dot (A - C)) - r^2 = 0
        // a                 b                             c
        // a = B dot B       b = 2 * B dot (A - C)         c = (A - C) dot (A - C) - r^2
        // a vector X - Y means the vector *from* Y to X:
        // since 0 + Y gets you to Y, then X - Y gets you to X
        // 0 + Y + (X - Y) = X
        // so A - C is from the center of the sphere to the ray's origin (i.e. the camera)

        /** @var Vec3 A - C */
        $rOrigin = $ray->origin();
        $fromCenterToRayOrigin = $rOrigin->subtract($this->center);

        $rDirection = $ray->direction();
        $a = $rDirection->dot($rDirection);
        $b = 2 * $fromCenterToRayOrigin->dot($rDirection);
        $c = $fromCenterToRayOrigin->dot($fromCenterToRayOrigin) - $this->radius**2;

        $discriminant = $b**2 - 4*$a*$c;

        // eliminated redundant 2's that cancel each-other out
        if ($discriminant > 0) {
            $tempT = (-$b - sqrt($discriminant)) / ($a);
            if ($tMin < $tempT && $tempT < $tMax) {
                $t = $tempT;
                $p = $ray->pointAtParameter($t);
                $fromCenterToHitPoint = $p->subtract($this->center);
                $normal = $fromCenterToHitPoint->divideByConstant($this->radius);

                $hitRecord = new HitRecord($t, $p, $normal);
                return [true, $hitRecord];
            }

            $tempT = (-$b + sqrt($discriminant)) / ($a);
            if ($tMin < $tempT && $tempT < $tMax) {
                $t = $tempT;
                $p = $ray->pointAtParameter($t);
                $fromCenterToHitPoint = $p->subtract($this->center);
                $normal = $fromCenterToHitPoint->divideByConstant($this->radius);

                $hitRecord = new HitRecord($t, $p, $normal);
                return [true, $hitRecord];
            }
        }
        return [false, null];
    }
}