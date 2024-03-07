<?php
namespace PrestigeSociety\Bosses\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\nbt\tag\CompoundTag;

class BossTimer extends Human{
        /** @var int */
        protected $gravity = 0;
        /** @var int */
        public int $max_ticks = 0;

        public function __construct(Location $location, Skin $skin, ?CompoundTag $nbt = null){
                parent::__construct($location, $skin, $nbt);

                $this->setCanSaveWithChunk(false);
        }

        public function onDespawn(){
        }

        protected function onDispose(): void{
                $this->onDespawn();
                parent::onDispose();
        }

        /**
         * @param int $tickDiff
         *
         * @return bool
         */
        public function entityBaseTick(int $tickDiff = 1): bool{
                if($this->ticksLived >= $this->max_ticks){
                        $this->flagForDespawn();
                }

                return parent::entityBaseTick($tickDiff);
        }
}