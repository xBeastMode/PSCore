<?php

namespace PrestigeSociety\Bosses\Entity\Custom\Alcapone;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\Living;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\AnimatePacket;
use pocketmine\world\Explosion;
use pocketmine\world\Position;
use PrestigeSociety\Core\Utils\Physics;
use PrestigeSociety\Core\Utils\RandomUtils;

class Hitman extends Human{

        /** @var Position|null */
        public ?Position $goal = null;
        /** @var Alcapone */
        public Alcapone $parent;
        /** @var bool */
        public bool $attacking = false;

        public function __construct(Location $location, Skin $skin, ?CompoundTag $nbt = null){
                parent::__construct($location, $skin, $nbt);

                $this->setCanSaveWithChunk(false);
        }

        /**
         * @return Position|null
         */
        public function getGoal(): ?Position{
                return $this->goal;
        }

        public function fight(): void{
                $pk = new AnimatePacket();
                $pk->actorRuntimeId = $this->getId();
                $pk->action = AnimatePacket::ACTION_SWING_ARM;
                $this->getWorld()->broadcastPacketToViewers($this->getPosition(), $pk);

                $target = $this->getTargetEntity();
                if($target !== null && $target instanceof Entity){
                        $target->attack(new EntityDamageByEntityEvent($this, $target, EntityDamageByEntityEvent::CAUSE_ENTITY_ATTACK, 1, [], 0.4));
                }
        }

        /**
         * @return bool
         */
        public function navigate(): bool{
                $goal = $this->getGoal();
                $position = $this->getPosition();
                
                if($goal === null || $goal->getWorld() !== $this->getWorld() || $this->getTargetEntity() === null) return false;

                [$yaw, $pitch] = Physics::calculateRotationEulerAngle($position->asVector3(), $this->getTargetEntity()->getPosition()->asVector3());

                if($goal instanceof Living && $goal->getPosition()->distance($position) <= 2){
                        return true;
                }

                $this->location->yaw = $yaw;
                $this->location->pitch = $pitch;

                $this->motion = Physics::calculateMotionVector($position, $goal, 0.5);

                if($this->isCollidedHorizontally){
                        $this->jumpVelocity = 2;
                        $this->jump();
                }else{
                        $this->motion->y = -1;
                }

                return false;
        }

        /**
         * @param int $tickDiff
         *
         * @return bool
         */
        public function entityBaseTick(int $tickDiff = 1): bool{
               $this->navigate();

                $this->setNameTag(RandomUtils::colorMessage("&l&eAlcapone's Hitman\n&4❤ &c" . $this->getHealth()));
               if($this->goal !== null && $this->attacking) $this->fight();

                return parent::entityBaseTick($tickDiff);
        }

        public function onDeath(): void{
                $explosion = new Explosion($this->getPosition(), 1);
                $explosion->explodeB();

                parent::onDeath();
        }

        public function getDrops(): array{
                return [];
        }
}