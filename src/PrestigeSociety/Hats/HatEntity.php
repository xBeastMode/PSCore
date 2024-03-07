<?php
namespace PrestigeSociety\Hats;
use JetBrains\PhpStorm\Pure;
use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\convert\RuntimeBlockMapping;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityLink;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
class HatEntity extends Entity{
        /** @var int */
        public $width = 1;
        /** @var float */
        public $height = 0.98;

        /** @var EntityLink */
        protected EntityLink $link;

        /**
         * @param int $blockId
         * @param int $blockMeta
         */
        public function setHatBlock(int $blockId, int $blockMeta = 0) : void{
                $this->getNetworkProperties()->setVector3(EntityMetadataProperties::RIDER_SEAT_POSITION, new Vector3(0, 0.4, 0));
                $this->getNetworkProperties()->setInt(EntityMetadataProperties::VARIANT, RuntimeBlockMapping::getInstance()->toRuntimeId(RandomUtils::legacyToInternalStateId($blockId, $blockMeta)));
        }

        /**
         * @param Player $player
         */
        public function sendSpawnPacket(Player $player): void{
                $packet = new AddActorPacket();
                $packet->actorUniqueId = $packet->actorRuntimeId = $this->getId();
                $packet->type = "minecraft:falling_block";
                $packet->position = $this->location->asVector3();
                $packet->motion = $this->getMotion();
                $packet->yaw = $this->location->yaw;
                $packet->headYaw = $this->location->yaw; //TODO
                $packet->pitch = $this->location->pitch;
                $packet->attributes = $this->attributeMap->getAll();
                $packet->metadata = $this->getNetworkProperties()->getAll();

                $packet->links[] = $this->link;
                $player->getNetworkSession()->sendDataPacket($packet);
        }

        /**
         * @param Player $player
         */
        public function link(Player $player){
                $this->link = new EntityLink($player->getId(), $this->getId(), EntityLink::TYPE_PASSENGER, false, true);
        }

        /**
         * @param Player $player
         */
        public function updateHat(Player $player){
                $this->location->x = $player->location->x;
                $this->location->y = $player->location->y + 1.98;
                $this->location->z = $player->location->z;
        }

        #[Pure] protected function getInitialSizeInfo(): EntitySizeInfo{
                return new EntitySizeInfo(1, 1);
        }

        public static function getNetworkTypeId(): string{
                return EntityIds::FALLING_BLOCK;
        }
}