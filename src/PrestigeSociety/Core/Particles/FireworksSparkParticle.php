<?php
namespace PrestigeSociety\Core\Particles;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\types\ParticleIds;
use pocketmine\world\particle\Particle;
class FireworksSparkParticle implements Particle{
        public function encode(Vector3 $pos): array{
                return [LevelEventPacket::standardParticle(ParticleIds::FIREWORKS_SPARK, 0, $pos)];
        }
}