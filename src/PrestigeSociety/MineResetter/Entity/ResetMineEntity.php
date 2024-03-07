<?php

namespace PrestigeSociety\MineResetter\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\nbt\tag\CompoundTag;
class ResetMineEntity extends Human{
        public function __construct(Location $location, Skin $skin, ?CompoundTag $nbt = null){
                parent::__construct($location, $skin, $nbt);

                $this->setScale(4);
                $this->setNameTagVisible();
                $this->setNameTagAlwaysVisible();
        }

        protected function updateMovement(bool $teleport = false): void{
        }

        public function setOnFire(int $seconds): void{
        }
}