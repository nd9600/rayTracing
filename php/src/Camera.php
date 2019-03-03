<?php
declare(strict_types=1);


namespace RayTracer;


class Camera
{
    /** @var Vec3 */
    private $lowerLeftCorner;

    /** @var Vec3 */
    private $horizontal;

    /** @var Vec3 */
    private $vertical;

    /** @var Vec3 */
    private $origin;

    public function __construct()
    {
        $this->lowerLeftCorner = new Vec3(-2, -1, -1);
        $this->horizontal = new Vec3(4, 0, 0);
        $this->vertical = new Vec3(0, 2, 0);
        $this->origin = new Vec3(0, 0, 0);
    }

    public function getRay(float $u, float $v): Ray
    {
        // direction = lowerLeftCorner + u*horizontal + v*vertical
        $direction = $this->lowerLeftCorner
            ->add($this->horizontal->multiplyByConstant($u))
            ->add($this->vertical->multiplyByConstant($v));
        $ray = new Ray($this->origin, $direction);
        return $ray;
    }
}