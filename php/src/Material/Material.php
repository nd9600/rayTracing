<?php
declare(strict_types=1);


namespace RayTracer\Material;


use RayTracer\HitRecord;
use RayTracer\Ray;

/**
 * Materials tell us how rays interact with the surface
 */
abstract class Material
{
    /**
     * Produces a scattered ray (or say it absorbed the incident ray), and, if scattered, say how much the ray should be attenuated
     * @param Ray $ray
     * @param HitRecord $hitRecord
     * @return array of [didScatter, $scatteredRay, $attenuationVec3]
     */
    public function scatter(Ray $ray, HitRecord $hitRecord): array
    {
        return [false, null, null];
    }
}