<?php
declare(strict_types=1);


namespace RayTracer\Hitable;


use RayTracer\Ray;

class HitableList extends Hitable
{
    /** @var Hitable[] */
    private $hitables;

    /**
     * @param Hitable[] $hitables
     */
    public function __construct(array $hitables)
    {
        $this->hitables = $hitables;
    }

    public function hit(Ray $ray, float $tMin, float $tMax): array
    {
       $hitAnything = false;
       $closestSoFar = $tMax;
       $hitRecordToReturn = null;

       // if a ray hits multiple things (remember, it goes on forever, doesn't stop when it hits something), we only care about the first thing it hits, because that's what you see
       foreach ($this->hitables as $hitable) {
           [$didHit, $hitRecord] = $hitable->hit($ray, $tMin, $closestSoFar);
           if ($didHit) {
               $hitAnything = true;
               $closestSoFar = $hitRecord->t;
               $hitRecordToReturn = $hitRecord;
           }
       }

       return [$hitAnything, $hitRecordToReturn];
    }
}