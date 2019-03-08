<?php

namespace RayTracer;


use RayTracer\Material\Material;

class HitRecord
{
    /** @var float */
    public $t;

    /** @var Vec3 */
    public $p;

    /** @var Vec3 */
    public $normal;
    
    /** @var Material
     * When a ray hits a surface (a particular sphere for example), the material in the hit_record will be set to point at the material the sphere was given when it was set up in main() ​when we start.
     * When color() gets the hit_record it can call member functions of the material to find out what ray, if any, is scattered
     */
    public $material;

    public function __construct(float $t, Vec3 $p, Vec3 $normal, Material $material)
    {
        $this->t = $t;
        $this->p = $p;
        $this->normal = $normal;
        $this->material = $material;
    }
}