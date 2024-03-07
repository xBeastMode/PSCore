<?php

namespace PrestigeSociety\Directions\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
class DirectionsEntity extends Human{
        public function __construct(Location $location, Skin $skin, ?CompoundTag $nbt = null){
                parent::__construct($location, $skin, $nbt);

                $this->setCanSaveWithChunk(false);
        }

        /**
         * @param Vector3 $vector3
         */
        public function moveTo(Vector3 $vector3){
                $this->location->x = $vector3->x;
                $this->location->y = $vector3->y;
                $this->location->z = $vector3->z;
                $this->updateMovement();
        }
}