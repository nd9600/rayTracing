<?php
declare(strict_types=1);


namespace RayTracer\Hitable;


use RayTracer\Ray;

abstract class Hitable
{
    /**
     * @param Ray $ray
     * @param float $tMin
     * @param float $tMax
     * @return array of [didHit, $hitRecord]
     */
    public function hit(Ray $ray, float $tMin, float $tMax): array
    {
        return [false, null];
    }
}