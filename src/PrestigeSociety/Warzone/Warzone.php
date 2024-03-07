<?php

namespace PrestigeSociety\Warzone;
use JetBrains\PhpStorm\Pure;
use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\utils\Random;
use pocketmine\world\Explosion;
use pocketmine\world\Position;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Core\Utils\StringUtils;
use PrestigeSociety\Core\Utils\TextureUtils;
use PrestigeSociety\InventoryMenu\TransactionData;
use PrestigeSociety\Warzone\Entity\FallingLootCrate;
use PrestigeSociety\Warzone\Entity\LootCrate;
class Warzone{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;
        /** @var FallingLootCrate|LootCrate|null */
        protected FallingLootCrate|null|LootCrate $crate = null;

        /** @var bool */
        protected bool $opened = false;
        /** @var Player[] */
        protected array $viewers = [];

        /**
         * Warzone constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
        }

        /**
         * @param string   $name
         * @param Position $position
         */
        public function addZone(string $name, Position $position){
                $this->core->module_configurations->warzone["crates"][$name] = [
                    $position->x, $position->y, $position->z, $position->getWorld()->getDisplayName()
                ];
                $this->core->module_configurations->saveWarzoneConfig();
        }

        /**
         * @param string $name
         */
        public function removeZone(string $name){
                unset($this->core->module_configurations->warzone["crates"][$name]);
                $this->core->module_configurations->saveWarzoneConfig();
        }

        /**
         * @param string $name
         *
         * @return Location|null
         */
        public function getZone(string $name): ?Location{
                $zone = $this->core->module_configurations->warzone["crates"][$name] ?? null;
                return $zone !== null ? RandomUtils::parseLocation($zone) : null;
        }

        /**
         * @return array
         */
        public function getRandomZone(): array{
                $zones = $this->core->module_configurations->warzone["crates"];
                $zone = array_rand($zones);

                return [$zone, $this->getZone($zone)];
        }

        /**
         * @return int
         */
        #[Pure] public function getCrateRespawnPeriod(): int{
                return StringUtils::stringToInteger($this->core->module_configurations->warzone["respawn_period"]);
        }

        /**
         * @param string|null $name
         *
         * @throws \JsonException
         */
        public function respawnLootCrate(?string $name = null): void{
                [$name, $position] = $name !== null ? [$name, $this->getZone($name)] : $this->getRandomZone();
                $this->createLootCrate($position, true, true, $name);
        }

        public function despawnLootCrate(): void{
                if($this->crate !== null && !$this->crate->isClosed()){
                        $this->crate->flagForDespawn();
                }
        }

        public function despawnForClose(): void{
                if($this->crate !== null && !$this->crate->isClosed()){
                        $this->crate->close();
                }
        }

        /**
         * @return Item[]
         */
        public function getLootCrateItems(?string $zone = null): array{
                $warzoneConfig = $this->core->module_configurations->warzone;

                $crate_items = $zone !== null ? $warzoneConfig["crate_items"][$zone] : $warzoneConfig["default_crate_items"];
                $crate_items = $crate_items ?? $warzoneConfig["default_crate_items"];

                $result = [];

                foreach($crate_items as $crate_item){
                        if(RandomUtils::randomFloat(1, 100) <= ((float) $crate_item["chance"])){
                                $splits = explode("$", $crate_item["item"]);

                                $type = $splits[0];
                                $item = $splits[1];

                                if($type === "i"){
                                        $result[] = RandomUtils::parseItemsWithEnchantments([$item])[0];
                                }elseif($type === "c"){
                                        $splits = explode(":", $item);

                                        $name = $splits[0];
                                        $count = StringUtils::stringToInteger($splits[1]);
                                        $value = StringUtils::stringToInteger($splits[2]);

                                        $result[] = $this->core->module_loader->custom_items->getCustomItem($name, $count, $value);
                                }
                        }
                }

                return $result;
        }

        public function closeLootCrateInventory(){
                foreach($this->viewers as $viewer){
                        $this->core->module_loader->inventory_menu->closeInventory($viewer);
                }
                $this->core->module_loader->inventory_menu->destroySyncedInventory($this->crate->getPosition());

                $this->viewers = [];
                $this->opened = false;
        }

        /**
         * @param Player      $player
         * @param string|null $zone
         */
        public function openLootCrateInventory(Player $player, ?string $zone = null){
                $chest_inventory = $this->core->module_loader->inventory_menu->openDoubleChestInventorySynced($player, function (TransactionData $data){
                        if(count($data->inventory->getContents()) <= 1){
                                $this->despawnLootCrate();
                                $this->closeLootCrateInventory();;
                        }
                        return false;
                }, [
                    "title" => "&l&8LOOT CRATE",
                    "position" => $this->crate->getPosition(),
                    "height" => 0,
                ]);

                if(!$this->opened){
                        $chest_inventory->clearAll();

                        $random = new Random();
                        $items = $this->getLootCrateItems($zone);

                        for($i = 0; $i < $chest_inventory->getSize(); $i++){
                                if(count($items) <= 0) break;
                                $chest_inventory->setItem($random->nextRange(0, $chest_inventory->getSize() - 1), array_shift($items));
                        }
                }

                $this->viewers[spl_object_hash($player)] = $player;
                $this->opened = true;
        }

        /**
         * @param Location    $position
         * @param bool        $falling
         * @param bool        $broadcast
         * @param string|null $zone
         *
         * @return Human
         *
         * @throws \JsonException
         */
        public function createLootCrate(Location $position, bool $falling = true, bool $broadcast = true, ?string $zone = null): Human{
                $this->despawnLootCrate();

                $position->getWorld()->getOrLoadChunkAtPosition($position);

                $geometry = TextureUtils::getGeometryData(__DIR__ . "/crate/" . ($falling ? "fallingcrate.json" : "crate.json"));
                $texture = TextureUtils::getTexture(__DIR__ . "/crate/crate.png");

                $nbt = RandomUtils::generateSkinCompoundTag($texture);

                $skin = new Skin("Standard_Custom", $texture, "", "geometry.crate", $geometry);

                /** @var LootCrate|FallingLootCrate $entity */
                $entity = $falling ? new FallingLootCrate($position, $skin, $nbt) : new LootCrate($position, $skin, $nbt);

                $entity->setNameTagVisible(false);
                $entity->setScale(3);

                $entity->canCollide = true;
                $entity->zone = $zone;

                $this->crate = $entity;
                $this->crate->spawnToAll();

                if($entity instanceof LootCrate){
                        $explosion = new Explosion($entity->getPosition(), 3);
                        $explosion->explodeB();

                        $entities = $entity->getWorld()->getNearbyEntities($entity->boundingBox->expandedCopy(10, 10, 10));
                        foreach($entities as $player){
                                if($player instanceof Player){
                                        RandomUtils::knockBack($player, $player->getLocation()->x - $entity->getLocation()->x, $player->getLocation()->z - $entity->getLocation()->z, 2);
                                }
                        }

                        $this->closeLootCrateInventory();
                }

                if($broadcast){
                        if($entity instanceof FallingLootCrate){
                                $message = $this->core->getMessage("warzone", "respawned_crate_fall");

                                foreach($this->core->getServer()->getOnlinePlayers() as $player){
                                        RandomUtils::playSound("ambient.weather.thunder", $player, 1000);
                                }
                        }else{
                                $message = $this->core->getMessage("warzone", "respawned_crate_land");

                                foreach($this->core->getServer()->getOnlinePlayers() as $player){
                                        RandomUtils::playSound("ambient.weather.lightning.impact", $player, 1000);
                                }
                        }

                        $message = str_replace("@zone", $zone, $message);
                        $this->core->getServer()->broadcastTitle(RandomUtils::colorMessage($message), "", 20, 20, 20);
                }

                return $this->crate;
        }
}
