<?php
namespace PrestigeSociety\Core;
use pocketmine\entity\Entity;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\SetActorLinkPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityLink;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\player\Player;
class EntityLinker{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $plugin;
        /** @var Entity[] */
        protected array $linked_riders = [];

        /**
         * EntityLinker constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->plugin = $core;
        }

        /**
         * @param Entity $player
         *
         * @return bool
         */
        public function unlinkRider(Entity $player): bool{
                if(isset($this->linked_riders[$player->getId()])){
                        $lastRidden = $this->linked_riders[$player->getId()];

                        $packet = new SetActorLinkPacket();

                        $packet->link = new EntityLink($lastRidden->getId(), $player->getId(), EntityLink::TYPE_REMOVE, true, true);
                        $player->getWorld()->getServer()->broadcastPackets($player->getWorld()->getServer()->getOnlinePlayers(), [$packet]);

                        unset($this->linked_riders[$player->getId()]);

                        return true;
                }
                return false;
        }

        /**
         * @param Player $player
         */
        public function sendRiderLinks(Player $player){
                foreach($this->linked_riders as $id => $entity){

                        $packet = new SetActorLinkPacket();
                        $packet->link = new EntityLink($entity->getId(), $player->getId(), EntityLink::TYPE_PASSENGER, true, true);

                        $player->getNetworkSession()->sendDataPacket($packet);
                }
        }

        /**
         * @param Entity $player
         * @param Entity $riding
         * @param bool   $unlinkPast
         */
        public function linkRider(Entity $player, Entity $riding, bool $unlinkPast = true){
                if($unlinkPast){
                        $this->unlinkRider($player);
                }

                if($player instanceof Player){
                        if($riding instanceof Player){
                                $player->getNetworkProperties()->setVector3(EntityMetadataProperties::RIDER_SEAT_POSITION, new Vector3(0, $riding->getEyeHeight(), 0));
                        }else{
                                $player->getNetworkProperties()->setVector3(EntityMetadataProperties::RIDER_SEAT_POSITION, new Vector3(0, $riding->getEyeHeight() + 1, 0));
                        }
                        $player->sendData($player->getWorld()->getServer()->getOnlinePlayers());
                }

                $packet = new SetActorLinkPacket();

                $packet->link = new EntityLink($riding->getId(), $player->getId(), EntityLink::TYPE_PASSENGER, true, true);
                $player->getWorld()->getServer()->broadcastPackets($player->getWorld()->getServer()->getOnlinePlayers(), [$packet]);

                $this->linked_riders[$player->getId()] = $riding;
        }

        /**
         * @param Entity $entity
         */
        public function tryUnlink(Entity $entity){
                foreach($this->linked_riders as $id => $entityR){
                        if($id === $entity->getId() || $entityR->getId() === $entity->getId()){
                                $packet = new SetActorLinkPacket();

                                $packet->link = new EntityLink($entityR->getId(), $id, EntityLink::TYPE_REMOVE, true, true);
                                $entity->getWorld()->getServer()->broadcastPackets($entity->getWorld()->getServer()->getOnlinePlayers(), [$packet]);

                                unset($this->linked_riders[$id]);
                        }
                }
        }
}