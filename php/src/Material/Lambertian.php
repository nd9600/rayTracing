<?php
declare(strict_types=1);


namespace RayTracer\Material;


use RayTracer\HitRecord;
use RayTracer\Ray;
use RayTracer\RayTracer;
use RayTracer\Vec3;

class Lambertian extends Material
{
    /** @var Vec3 */
    private $albedo;
    
    public function __construct(Vec3 $albedo) {
        $this->albedo = $albedo;
    }
    
    /**
     * Can either scatter always and attenuate by its reflectance R, or it can scatter with no attenuation but absorb the fraction 1-R of the rays.
     * Or it could be a mixture of those strategies
     * @param Ray $ray
     * @param HitRecord $hitRecord
     * @return array
     */
    public function scatter(Ray $ray, HitRecord $hitRecord): array
    {
        // could just as well only scatter with some probability p and have attenuation be albedo/p
        
        $hitRecordP = $hitRecord->p;
        $hitRecordNormal = $hitRecord->normal;
    
        $centerOfUnitSphereTangentToHitpoint = $hitRecordP->add($hitRecordNormal);
        $randomReflectionPoint = $centerOfUnitSphereTangentToHitpoint->add(RayTracer::randomPointInUnitSphere());
    
        $fromHitpointToRandomReflectionPoint = $randomReflectionPoint->subtract($hitRecordP);
        $scatteredRay = new Ray($hitRecordP, $fromHitpointToRandomReflectionPoint);
        $attenuationVector = $this->albedo;
        return [true, $scatteredRay, $attenuationVector];
    }
}