<?php
declare(strict_types=1);

namespace RayTracer;


use ArrayAccess;

class Vec3 implements ArrayAccess
{
    /** @var float */
    private $e0;
    
    /** @var float */
    private $e1;
    
    /** @var float */
    private $e2;
    
    public function __construct(float $e0, float $e1, float $e2) {
        $this->e0 = $e0;
        $this->e1 = $e1;
        $this->e2 = $e2;
    }
    
    ########################################
    # colours
    ########################################
    
    public function x(): float{ return $this->e0; }
    public function y(): float{ return $this->e1; }
    public function z(): float{ return $this->e2; }
    
    public function r(): float{ return $this->e0; }
    public function g(): float{ return $this->e1; }
    public function b(): float{ return $this->e2; }
    
    ########################################
    # so you can use it like an array
    ########################################
    
    public function offsetSet($offset, $value): void{switch ($offset) {case 0:$this->e0 = $value;break;case 1:$this->e1 = $value;break;case 2:$this->e2 = $value;break;default:break;} }
    
    public function offsetExists($offset): bool{return true; }
    
    public function offsetUnset($offset): void{switch ($offset) {case 0:$this->e0 = null;break;case 1:$this->e1 = null;break;case 2:$this->e2 = null;break;default:break;} }
    
    public function offsetGet($offset): ?float{switch ($offset) {case 0:return $this->e0;break;case 1:return $this->e1;case 2:return $this->e2;default:return null;} }
    
    ########################################
    # arithmetic operators
    ########################################
    
    public function minus(): Vec3{ return new Vec3(-$this->e0, -$this->e1, -$this->e2); }
    public function add(Vec3 $v2): Vec3{ return new Vec3($this->e0 + $v2[0], $this->e1 + $v2[1], $this->e2 + $v2[2]); }
    public function subtract(Vec3 $v2): Vec3{ return new Vec3($this->e0 - $v2[0], $this->e1 - $v2[1], $this->e2 - $v2[2]); }
    public function multiply(Vec3 $v2): Vec3{ return new Vec3($this->e0 * $v2[0], $this->e1 * $v2[1], $this->e2 * $v2[2]); }
    public function multiplyByConstant(float $t): Vec3{ return new Vec3($this->e0 * $t, $this->e1 * $t, $this->e2 * $t); }
    public function divide(Vec3 $v2): Vec3{ return new Vec3($this->e0 / $v2[0], $this->e1 / $v2[1], $this->e2 / $v2[2]); }
    public function divideByConstant(float $t): Vec3{ return new Vec3($this->e0 / $t, $this->e1 / $t, $this->e2 / $t); }
    
    public function dot(Vec3 $v2): float{return ($this[0] * $v2[0]) + ($this[1] * $v2[1]) + ($this[2] * $v2[2]); }
    public function cross(Vec3 $v2): Vec3
    {
        $newE0 = $this[1] * $v2[2] - $this[2] * $v2[1];
        $newE1 = $this[0] * $v2[2] - $this[2] * $v2[0];
        $newE2 = $this[0] * $v2[1] - $this[1] * $v2[0];
        return new Vec3($newE0, $newE1, $newE2);
    }
    
    ########################################
    # length functions
    ########################################
    
    public function length(): float{return sqrt(($this->e0 * $this->e0) + ($this->e1 * $this->e1) + ($this->e2 * $this->e2)); }
    public function squaredLength(): float{return ($this->e0 * $this->e0) + ($this->e1 * $this->e1) + ($this->e2 * $this->e2); }
    public function makeUnitVector(): Vec3
    {
        $k = 1.0 / $this->length();
        return new Vec3($this->e0 * $k, $this->e1 * $k, $this->e2 * $k);
    }
}