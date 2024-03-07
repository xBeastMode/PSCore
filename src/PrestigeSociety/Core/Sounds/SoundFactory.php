<?php
namespace PrestigeSociety\Core\Sounds;
use JetBrains\PhpStorm\Pure;
use PrestigeSociety\Core\Utils\SoundNames;
class SoundFactory{
        public static function ENDERCHEST_OPEN_SOUND(float $volume = 1000, float $pitch = 1): CoreSound{
                return new class($volume, $pitch) extends CoreSound{
                        #[Pure] public function __construct(float $volume = 1000, float $pitch = 1){
                                parent::__construct(SoundNames::SOUND_RANDOM_ENDERCHESTOPEN, $volume, $pitch);
                        }
                };
        }

        public static function ENDERCHEST_CLOSE_SOUND(float $volume = 1000, float $pitch = 1): CoreSound{
                return new class($volume, $pitch) extends CoreSound{
                        #[Pure] public function __construct(float $volume = 1000, float $pitch = 1){
                                parent::__construct(SoundNames::SOUND_RANDOM_ENDERCHESTCLOSED, $volume, $pitch);
                        }
                };
        }
}