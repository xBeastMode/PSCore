<?php
namespace PrestigeSociety\Core\Utils;
use JetBrains\PhpStorm\Pure;
use pocketmine\math\Vector3;
class Physics{
        /**
         * Calculates a motion vector given the parameters.
         *
         * @param Vector3 $current
         * @param Vector3 $target
         * @param float   $speed
         * @param bool    $ignoreUpward
         * @param float   $offset
         *
         * @return Vector3
         */
        #[Pure] public static function calculateMotionVector(Vector3 $current, Vector3 $target, float $speed = 0.25, bool $ignoreUpward = true, float $offset = 0.7): Vector3{
                $x = $target->x - $current->x;
                $y = $target->y - $current->y;
                $z = $target->z - $current->z;
                $squared = ($x ** 2) + ($z ** 2);
                $magnitude = sqrt($squared);
                $motionVector = new Vector3(0, 0, 0);
                if($squared >= $offset && $magnitude !== 0.0){
                        $xComponent = $x / $magnitude;
                        $motionVector->x = $speed * $xComponent;
                        $zComponent = $z / $magnitude;
                        $motionVector->z = $speed * $zComponent;
                }
                if($ignoreUpward === false && (float)$y !== 0.0){
                        $motionVector->y = $speed * $y;
                }
                return $motionVector;
        }

        /**
         * Calculates the rotational euler angles given the parameters.
         *
         * @param Vector3 $current
         * @param Vector3 $target
         * @param bool    $invertRotation
         *
         * @return array
         */
        public static function calculateRotationEulerAngle(Vector3 $current, Vector3 $target, bool $invertRotation = false): array{
                $x = $target->x - $current->x;
                $y = $target->y - $current->y;
                $z = $target->z - $current->z;
                $squared = ($x ** 2) + ($z ** 2);
                $magnitude = sqrt($squared);
                $arcTangentXZ = atan2(-$x, $z);
                $yaw = rad2deg($arcTangentXZ);
                if($invertRotation === true){
                        $yaw += 180;
                }
                $arcTangentY = -atan2($y, $magnitude);
                $pitch = rad2deg($arcTangentY);
                return [$yaw, $pitch];
        }
}