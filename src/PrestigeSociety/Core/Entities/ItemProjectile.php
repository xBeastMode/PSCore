<?php
namespace PrestigeSociety\Core\Entities;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use PrestigeSociety\Core\PrestigeSocietyCore;
class ItemProjectile extends Human{
        /** @var PrestigeSocietyCore|null */
        public ?PrestigeSocietyCore $mainInstance = null;
        /** @var Item */
        public Item $itemProjectile;
        /** @var Entity|null */
        public ?Entity $shootingEntity = null;

        public float $width = 0.5;
        public float $height = 1.975;

        /**
         * ItemProjectile constructor.
         *
         * @param Location    $level
         * @param Skin        $skin
         * @param CompoundTag $nbt
         */
        public function __construct(Location $level, Skin $skin, CompoundTag $nbt){
                $this->mainInstance = PrestigeSocietyCore::getInstance();
                $this->itemProjectile = Item::nbtDeserialize($nbt->getListTag("HandItems")->getValue()[0]);

                parent::__construct($level, $skin);

                $this->setCanSaveWithChunk(false);
        }

        /**
         * @param int $currentTick
         *
         * @return bool
         */
        public function onUpdate(int $currentTick): bool{
                if($this->shootingEntity === null || $this->onGround || $this->ticksLived > (20 * 3)){
                        $this->flagForDespawn();
                }
                return parent::onUpdate($currentTick);
        }

        /**
         * @param Player $player
         */
        public function onCollideWithPlayer(Player $player): void{
                if($player !== $this->shootingEntity){
                        if(!$this->onGround){
                                $owner = $this->shootingEntity;
                                $damage = $this->itemProjectile->getAttackPoints();

                                $player->attack(new EntityDamageByChildEntityEvent($owner, $this, $player, EntityDamageEvent::CAUSE_PROJECTILE, $damage));
                        }
                        $this->flagForDespawn();
                }
        }

        /**
         * @param Player $player
         */
        public function sendSpawnPacket(Player $player): void{
                parent::sendSpawnPacket($player);

                $this->getInventory()->setHeldItemIndex(0);
                $this->getInventory()->setItem(0, $this->itemProjectile);
        }
}