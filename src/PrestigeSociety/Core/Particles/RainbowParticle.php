<?php
namespace PrestigeSociety\Core\Particles;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\types\ParticleIds;
use pocketmine\world\particle\Particle;
class RainbowParticle implements Particle{
        protected array $colors = [
            [255, 0, 0],
            [255, 127, 0],
            [255, 255, 0],
            [0, 255, 0],
            [0, 0, 255],
            [46, 43, 95],
            [139, 0, 255]
        ];

        protected int $index = 0;
        protected int $r, $g, $b;

        public function encode(Vector3 $pos): array{
                if($this->index++ >= count($this->colors) - 1){
                        $this->index = 0;
                }

                $colors = $this->colors[$this->index];

                $this->r = $colors[0];
                $this->g = $colors[1];
                $this->b = $colors[2];

                $data = ((255 & 0xff) << 24) | (($this->r & 0xff) << 16) | (($this->g & 0xff) << 8) | ($this->b & 0xff);

                return [LevelEventPacket::standardParticle(ParticleIds::DUST, $data, $pos)];
        }
}