<?php
namespace PrestigeSociety\InventoryMenu;
use Closure;
use pocketmine\block\Block;
use pocketmine\block\inventory\ChestInventory;
use pocketmine\block\inventory\DoubleChestInventory;
use pocketmine\block\tile\Chest;
use pocketmine\block\tile\Tile;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Location;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\network\mcpe\protocol\BlockActorDataPacket;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\player\Player;
use pocketmine\world\Position;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\InventoryMenu\Inventory\FakeChestInventory;
use PrestigeSociety\InventoryMenu\Inventory\FakeDoubleChestInventory;
use PrestigeSociety\InventoryMenu\Task\InventoryOpenTask;
class InventoryMenu{
        /** @var Closure[] */
        protected array $inventory_callbacks;
        /** @var Tile[][]|Block[][]|ChestInventory[][] */
        protected array $inventory_menus = [];
        /** @var Tile[][][]|Block[][][]|DoubleChestInventory[][] */
        protected array $double_inventory_menus = [];
        /** @var Closure[] */
        protected array $inventory_close_callback;
        /** @var Closure[] */
        protected array $drop_item_callback;

        /** @var FakeChestInventory[]|FakeDoubleChestInventory[][] */
        protected array $synced_inventory = [];

        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /**
         * InventoryMenu constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;

                $this->core->getServer()->getPluginManager()->registerEvents(new EventListener($core), $core);
        }

        /**
         * @param InventoryTransactionEvent $event
         */
        public function onInventoryTransaction(InventoryTransactionEvent $event){
                $transaction = $event->getTransaction();
                $player = $transaction->getSource();
                $actions = $transaction->getActions();

                $hash = spl_object_hash($player);
                if(isset($this->inventory_callbacks[$hash])){
                        foreach($actions as $action){
                                if(!$action instanceof SlotChangeAction) continue;

                                if(isset($this->inventory_menus[$hash]) && $this->inventory_menus[$hash]["chest_inventory"] === $action->getInventory()){
                                        $transaction_data = new TransactionData($player, $action->getInventory(), $action->getSlot(), $action->getSourceItem(), $action->getTargetItem());
                                        if($this->inventory_callbacks[$hash]($transaction_data)){
                                                $event->cancel();
                                        }
                                }else if(isset($this->double_inventory_menus[$hash]) && $this->double_inventory_menus[$hash]["chest_inventory"] === $action->getInventory()){
                                        $transaction_data = new TransactionData($player, $action->getInventory(), $action->getSlot(), $action->getSourceItem(), $action->getTargetItem());
                                        if($this->inventory_callbacks[$hash]($transaction_data)){
                                                $event->cancel();
                                        }
                                }
                        }
                }
        }

        /**
         * @param PlayerDropItemEvent $event
         */
        public function onPlayerDropItem(PlayerDropItemEvent $event){
                $player = $event->getPlayer();
                $item = $event->getItem();

                $hash = spl_object_hash($player);
                if(isset($this->drop_item_callback[$hash]) && $this->drop_item_callback[$hash]($player, $item)){
                        $event->cancel();
                }
        }

        /**
         * @param Player   $player
         * @param Closure $callback
         */
        public function setCloseCallback(Player $player, Closure $callback){
                $this->inventory_close_callback[spl_object_hash($player)] = $callback;
        }

        /**
         * @param Player   $player
         * @param Closure $callback
         */
        public function setDropItemCallback(Player $player, Closure $callback){
                $this->drop_item_callback[spl_object_hash($player)] = $callback;
        }

        /**
         * @param Player $player
         */
        public function closeInventory(Player $player){
                $hash = spl_object_hash($player);

                if(isset($this->inventory_callbacks[$hash]) && isset($this->double_inventory_menus[$hash])){
                        $inventory_menu = $this->double_inventory_menus[$hash];

                        if(isset($this->inventory_close_callback[$hash])){
                                $this->inventory_close_callback[$hash]();
                        }

                        RandomUtils::sendBlocks([$player], $inventory_menu["blocks"]);
                        unset($this->inventory_callbacks[$hash], $this->double_inventory_menus[$hash], $this->inventory_close_callback[$hash], $this->drop_item_callback[$hash]);
                }

                if(isset($this->inventory_callbacks[$hash]) && isset($this->inventory_menus[$hash])){
                        $inventory_menu = $this->inventory_menus[$hash];

                        if(isset($this->inventory_close_callback[$hash])){
                                $this->inventory_close_callback[$hash]();
                        }

                        RandomUtils::sendBlocks([$player], [$inventory_menu["old_block"]]);
                        unset($this->inventory_callbacks[$hash], $this->inventory_menus[$hash], $this->inventory_close_callback[$hash], $this->drop_item_callback[$hash]);
                }
        }

        /**
         * @param Player  $player
         * @param Closure $callback
         * @param array   $options
         *
         * @return ChestInventory|null
         */
        public function openInventory(Player $player, Closure $callback, array $options = []): ?ChestInventory{
                $height = ($optios["height"] ?? 2);
                /** @var Position $position */
                $position = $options["position"] ?? $player->getPosition();
                $vector = $position->floor()->add(0, 2 + $height, 0);

                $hash = spl_object_hash($player);
                $old_block = $player->getWorld()->getBlock($vector);

                $block = VanillaBlocks::CHEST();
                $block->position($player->getWorld(), $vector->x, $vector->y, $vector->z);

                RandomUtils::sendBlocks([$player], [$block]);
                $namedtag = CompoundTag::create();

                $namedtag->setTag(Chest::TAG_ITEMS, new ListTag([]));
                $namedtag->setString(Chest::TAG_CUSTOM_NAME, RandomUtils::colorMessage($options["title"] ?? "&8&lMENU"));

                $tile = new Chest($player->getWorld(), $vector);

                if($tile instanceof Chest){
                        $chest_inventory = new FakeChestInventory($block->getPosition(), $options["open_sound"] ?? null, $options["close_sound"] ?? null);
                        $chest_inventory->setContents($options["items"] ?? []);

                        $player->setCurrentWindow($chest_inventory);

                        $this->inventory_menus[$hash] = [
                            "tile" => $tile,
                            "old_block" => $old_block,
                            "chest_inventory" => $chest_inventory,
                        ];
                        $this->inventory_callbacks[$hash] = $callback;

                        return $chest_inventory;
                }
                return null;
        }

        /**
         * @param Player   $player
         * @param Closure $callback
         * @param array    $options
         *
         * @return null|DoubleChestInventory
         */
        public function openDoubleChestInventory(Player $player, Closure $callback, array $options = []): ?DoubleChestInventory{
                $height = ($optios["height"] ?? 2);
                /** @var Position $position */
                $position = $options["position"] ?? $player->getPosition();
                $vector = $position->floor()->add(0, 2 + $height, 0);

                $hash = spl_object_hash($player);
                $block = VanillaBlocks::CHEST();

                if(!isset($this->double_inventory_menus[$hash])){
                        $this->double_inventory_menus[$hash] = ["tiles" => [], "blocks" => []];
                }

                $positions = [$vector, $vector->add(1, 0, 0)];
                foreach($positions as $index => $position){
                        $block->position($player->getWorld(), $position->x, $position->y, $position->z);
                        $namedtag = CompoundTag::create();

                        $namedtag->setString(Chest::TAG_CUSTOM_NAME, RandomUtils::colorMessage($options["title"] ?? "&8&lMENU"));
                        $namedtag->setInt(Chest::TAG_PAIRX, $position->x + ($index === 0 ? 1 : -1));
                        $namedtag->setInt(Chest::TAG_PAIRZ, $position->z);

                        $tile = new Chest($player->getWorld(), $position);

                        $this->double_inventory_menus[$hash]["tiles"][] = $tile;
                        $this->double_inventory_menus[$hash]["blocks"][] = $player->getWorld()->getBlock($position);
                        $this->double_inventory_menus[$hash]["inventories"][] = new FakeChestInventory($block->getPosition());

                        RandomUtils::sendBlocks([$player], [$block]);
                        $player->getNetworkSession()->sendDataPacket(BlockActorDataPacket::create(BlockPosition::fromVector3($position), new CacheableNbt($namedtag)));

                }

                /** @var Chest[] $tiles */
                $tiles = $this->double_inventory_menus[$hash]["tiles"];
                $tiles[0]->pairWith($tiles[1]);

                $params = array_merge($this->double_inventory_menus[$hash]["inventories"], [$options["open_sound"] ?? null, $options["close_sound"] ?? null]);

                $chest_inventory = new FakeDoubleChestInventory(...$params);
                $chest_inventory->setContents($options["items"] ?? []);

                $this->double_inventory_menus[$hash]["chest_inventory"] = $chest_inventory;
                $this->inventory_callbacks[spl_object_hash($player)] = $callback;

                $this->core->getScheduler()->scheduleDelayedTask(new InventoryOpenTask($this->core, $player, $chest_inventory), $player->getNetworkSession()->getPing() < 300 ? 5 : 0);

                return $chest_inventory;
        }

        /**
         * @param Vector3 $vector
         */
        public function destroySyncedInventory(Vector3 $vector){
                unset($this->synced_inventory[RandomUtils::vectorToString($vector->floor())]);
        }

        /**
         * @param Player  $player
         * @param Closure $callback
         * @param array   $options
         *
         * @return ChestInventory|null
         */
        public function openInventorySynced(Player $player, Closure $callback, array $options = []): ?ChestInventory{
                $height = ($optios["height"] ?? 2);
                /** @var Position $position */
                $position = $options["position"] ?? $player->getPosition();
                $vector = $position->floor()->add(0, 2 + $height, 0);

                $hash = spl_object_hash($player);
                $old_block = $player->getWorld()->getBlock($vector);

                $block = VanillaBlocks::CHEST();
                $block->position($player->getWorld(), $vector->x, $vector->y, $vector->z);

                RandomUtils::sendBlocks([$player], [$block]);
                $namedtag = CompoundTag::create();

                $namedtag->setTag(Chest::TAG_ITEMS, new ListTag([]));
                $namedtag->setString(Chest::TAG_CUSTOM_NAME, RandomUtils::colorMessage($options["title"] ?? "&8&lMENU"));

                $tile = new Chest($player->getWorld(), $vector);

                $index = RandomUtils::vectorToString($vector);
                $opened = isset($this->synced_inventory[$index]);

                $chest_inventory = $this->synced_inventory[$index] ?? new FakeChestInventory($block->getPosition(), $options["open_sound"] ?? null, $options["close_sound"] ?? null);
                $this->synced_inventory[$index] = $chest_inventory;

                if(!$opened) $chest_inventory->setContents($options["items"] ?? []);

                $player->setCurrentWindow($chest_inventory);

                $this->inventory_menus[$hash] = [
                    "tile" => $tile,
                    "old_block" => $old_block,
                    "chest_inventory" => $chest_inventory,
                ];
                $this->inventory_callbacks[$hash] = $callback;

                return $chest_inventory;
        }

        /**
         * @param Player   $player
         * @param Closure $callback
         * @param array    $options
         *
         * @return null|DoubleChestInventory
         */
        public function openDoubleChestInventorySynced(Player $player, Closure $callback, array $options = []): ?DoubleChestInventory{
                $height = ($optios["height"] ?? 2);
                /** @var Position $position */
                $position = $options["position"] ?? $player->getPosition();
                $vector = $position->floor()->add(0, 2 + $height, 0);

                $hash = spl_object_hash($player);
                $block = VanillaBlocks::CHEST();

                if(!isset($this->double_inventory_menus[$hash])){
                        $this->double_inventory_menus[$hash] = ["tiles" => [], "blocks" => []];
                }

                $positions = [$vector, $vector->add(1, 0, 0)];
                foreach($positions as $index => $position){
                        $block->position($player->getWorld(), $position->x, $position->y, $position->z);

                        $namedtag = CompoundTag::create();

                        $namedtag->setString(Chest::TAG_CUSTOM_NAME, RandomUtils::colorMessage($options["title"] ?? "&8&lMENU"));
                        $namedtag->setInt(Chest::TAG_PAIRX, $position->x + ($index === 0 ? 1 : -1));
                        $namedtag->setInt(Chest::TAG_PAIRZ, $position->z);

                        $tile = new Chest($player->getWorld(), $position);

                        $this->double_inventory_menus[$hash]["tiles"][] = $tile;
                        $this->double_inventory_menus[$hash]["blocks"][] = $player->getWorld()->getBlock($position);
                        $this->double_inventory_menus[$hash]["inventories"][] = new FakeChestInventory($block->getPosition());

                        RandomUtils::sendBlocks([$player], [$block]);
                        $player->getNetworkSession()->sendDataPacket(BlockActorDataPacket::create(BlockPosition::fromVector3($position), new CacheableNbt($namedtag)));
                }

                /** @var Chest[] $tiles */
                $tiles = $this->double_inventory_menus[$hash]["tiles"];
                $tiles[0]->pairWith($tiles[1]);

                $params = array_merge($this->double_inventory_menus[$hash]["inventories"], [$options["open_sound"] ?? null, $options["close_sound"] ?? null]);

                $index = RandomUtils::vectorToString($vector);
                $opened = isset($this->synced_inventory[$index]);

                $chest_inventory = $this->synced_inventory[$index] ?? new FakeDoubleChestInventory(...$params);
                $this->synced_inventory[$index] = $chest_inventory;

                if(!$opened) $chest_inventory->setContents($options["items"] ?? []);

                $this->double_inventory_menus[$hash]["chest_inventory"] = $chest_inventory;
                $this->inventory_callbacks[spl_object_hash($player)] = $callback;

                $this->core->getScheduler()->scheduleDelayedTask(new InventoryOpenTask($this->core, $player, $chest_inventory), $player->getNetworkSession()->getPing() < 300 ? 5 : 0);

                return $chest_inventory;
        }
}