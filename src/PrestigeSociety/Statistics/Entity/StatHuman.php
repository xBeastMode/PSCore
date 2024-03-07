<?php
namespace PrestigeSociety\Statistics\Entity;
use pocketmine\entity\Human;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\AnimatePacket;
class StatHuman extends Human{
        public string $type = "";
        public int $place = 0;
        public string $statsProfile = "";

        public function doPunchAnimation(): void{
                $pk = new AnimatePacket();
                $pk->actorRuntimeId = $this->getId();
                $pk->action = AnimatePacket::ACTION_SWING_ARM;
                $this->getWorld()->broadcastPacketToViewers($this->location, $pk);
        }

        protected function initEntity(CompoundTag $nbt): void{
                parent::initEntity($nbt);

                $this->type = $nbt->getString("type", "");
                $this->place = $nbt->getInt("place", 0);
                $this->statsProfile = $nbt->getString("statsProfile", "");

        }

        public function saveNBT(): CompoundTag{
                $nbt = parent::saveNBT();

                $nbt->setString("type", $this->type);
                $nbt->setInt("place", $this->place);
                $nbt->setString("statsProfile", $this->statsProfile);

                return $nbt;
        }

        /**
         * @param int $currentTick
         *
         * @return bool
         */
        public function onUpdate(int $currentTick): bool{
                if($currentTick % 20 === 0){
                        $this->location->yaw = $this->location->yaw >= 360 ? 0 : $this->location->yaw + mt_rand(-100, 100);
                }
                if($currentTick % 5 === 0){
                        $this->doPunchAnimation();
                        if(mt_rand(1, 3) === 1){
                                $this->jump();
                        }

                        if(mt_rand(1, 3) === 1){
                                $this->setSneaking(true);
                        }else{
                                $this->setSneaking(false);
                        }
                }
                return parent::onUpdate($currentTick);
        }
}