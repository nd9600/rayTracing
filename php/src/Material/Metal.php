<?php
declare(strict_types=1);


namespace RayTracer\Material;


use RayTracer\HitRecord;
use RayTracer\Ray;
use RayTracer\Vec3;

class Metal extends Material
{
    /** @var Vec3 */
    private $albedo;
    
    public function __construct(Vec3 $albedo) {
        $this->albedo = $albedo;
    }
    
    public function scatter(Ray $ray, HitRecord $hitRecord): array
    {
        $hitRecordP = $hitRecord->p;
        $hitRecordNormal = $hitRecord->normal;
        
        $reflectedRay = $this->reflect($ray->direction()->makeUnitVector(), $hitRecordNormal);
    
        $scatteredRay = new Ray($hitRecordP, $reflectedRay);
        $attenuationVector = $this->albedo;
        $didScatter = $scatteredRay->direction()->dot($hitRecordNormal) > 0;
        return [$didScatter, $scatteredRay, $attenuationVector];
    }
    
    private function reflect(Vec3 $v, Vec3 $normal): Vec3
    {
        // if vector v hits a point at angle theta to the normal, it reflects at the same angle away at vector R
        // R is v + 2B, where B is the vector to get from 2v back to the surface (see diagram on page 26)
        // the length of B is dot(v, N), and v points in, so R = v - 2 *  dot(v, N) * N
        $lengthOfB = $v->dot($normal);
        $twoB = $normal->multiplyByConstant($lengthOfB)->multiplyByConstant(2);
        return $v->subtract($twoB);
    }
}