<?php

namespace RayTracer;


class HitRecord
{
    /** @var float */
    public $t;

    /** @var Vec3 */
    public $p;

    /** @var Vec3 */
    public $normal;

    public function __construct(float $t, Vec3 $p, Vec3 $normal)
    {
        $this->t = $t;
        $this->p = $p;
        $this->normal = $normal;
    }
}