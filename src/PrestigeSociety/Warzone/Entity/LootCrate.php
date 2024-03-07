<?php
namespace PrestigeSociety\Warzone\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use PrestigeSociety\Core\PrestigeSocietyCore;

class LootCrate extends Human{
        /** @var int */
        protected $gravity = 0;
        /** @var string|null */
        public ?string $zone = null;

        public function __construct(Location $location, Skin $skin, ?CompoundTag $nbt = null){
                parent::__construct($location, $skin, $nbt);

                $this->setCanSaveWithChunk(false);
        }

        /**
         * @param bool $teleport
         */
        public function updateMovement(bool $teleport = false): void{
        }
        /**
         * @param int $tickDiff
         *
         * @return bool
         */
        protected function doOnFireTick(int $tickDiff = 1): bool{
                return false;
        }

        /**
         * @param int $tickDiff
         *
         * @return bool
         */
        public function entityBaseTick(int $tickDiff = 1): bool{
                parent::entityBaseTick($tickDiff);
                return true;
        }

        /**
         * @param EntityDamageEvent $source
         */
        public function attack(EntityDamageEvent $source): void{
                $source->cancel();

                if($source instanceof EntityDamageByEntityEvent){
                        $player = $source->getDamager();
                        if($player instanceof Player){
                                PrestigeSocietyCore::getInstance()->module_loader->warzone->openLootCrateInventory($player, $this->zone);
                        }
                }
        }
}