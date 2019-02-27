<?php
declare(strict_types=1);


namespace RayTracer;


class Ray
{
    /** @var Vec3 */
    private $a;
    
    /** @var Vec3 */
    private $b;
    
    public function __construct(Vec3 $a, Vec3 $b) {
        $this->a = $a;
        $this->b = $b;
    }
    
    public function origin(): Vec3{return $this->a; }
    public function direction(): Vec3{return $this->b; }
    public function pointAtParameter(float $t): Vec3{return $this->a->add($this->b->multiplyByConstant($t)); }
}