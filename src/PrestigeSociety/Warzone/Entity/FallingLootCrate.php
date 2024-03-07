<?php
namespace PrestigeSociety\Warzone\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\nbt\tag\CompoundTag;
use PrestigeSociety\Core\PrestigeSocietyCore;
class FallingLootCrate extends Human{
        protected $gravity = 0.01;
        /** @var string|null */
        public ?string $zone = null;

        public function __construct(Location $location, Skin $skin, ?CompoundTag $nbt = null){
                parent::__construct($location, $skin, $nbt);

                $this->setCanSaveWithChunk(false);
        }

        /**
         * @param int $tickDiff
         *
         * @return bool
         *
         * @throws \JsonException
         */
        public function entityBaseTick(int $tickDiff = 1): bool{
                if($this->onGround){
                        PrestigeSocietyCore::getInstance()->module_loader->warzone->createLootCrate($this->getLocation(), false, true, $this->zone);
                }
                parent::entityBaseTick($tickDiff);
                return true;
        }

        /**
         * @param EntityDamageEvent $source
         */
        public function attack(EntityDamageEvent $source): void{
                $source->cancel();
        }
}