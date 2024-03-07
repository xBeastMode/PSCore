<?php

namespace PrestigeSociety\Core\Sounds;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\world\sound\Sound;
abstract class CoreSound implements Sound{
        /** @var string */
        private string $sound_name;
        /** @var float */
        private float $volume;
        /** @var float */
        private float $pitch;

        public function __construct(string $sound_name, float $volume = 1000, float $pitch = 1){
                $this->sound_name = $sound_name;
                $this->volume = $volume;
                $this->pitch = $pitch;
        }

        /**
         * @return string
         */
        public function getSoundName(): string{
                return $this->sound_name;
        }

        /**
         * @return float|int
         */
        public function getVolume(): float|int{
                return $this->volume;
        }

        public function getPitch() : float{
                return $this->pitch;
        }

        public function encode(Vector3 $pos): array{
                return [PlaySoundPacket::create($this->sound_name, $pos->x, $pos->y, $pos->z, $this->volume, $this->pitch)];
        }
}