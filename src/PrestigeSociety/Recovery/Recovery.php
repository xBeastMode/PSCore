<?php
namespace PrestigeSociety\Recovery;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\player\Player;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\DataModels\RecoveryModel;
use PrestigeSociety\InventoryMenu\TransactionData;
class Recovery{
        const PAGE_SIZE = 52;
        const LAST_PAGE_INDEX = 45;
        const NEXT_PAGE_INDEX = 53;

        const ITEM_ID_TAG = "__item_id__";

        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /** @var Item[][] */
        protected array $viewing = [];
        /** @var int[] */
        protected array $page = [];

        /**
         * Recovery constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
                $this->core->getServer()->getPluginManager()->registerEvents(new RecoveryListener($core), $core);
        }

        /**
         * @param Item $item
         *
         * @return bool
         */
        public static function hasItemId(Item $item): bool{
                return $item->getNamedTag()->getString(self::ITEM_ID_TAG, false) != false;
        }

        /**
         * @param Item $item
         *
         * @return string
         */
        public static function getItemId(Item $item): string{
                $id = $item->getNamedTag()->getString(self::ITEM_ID_TAG, uniqid("", true));
                $item->getNamedTag()->setString(self::ITEM_ID_TAG, $id);
                return $id;
        }

        /**
         * @param       $player
         * @param array $items
         * @param bool  $checkRepeated
         */
        public function backupItems($player, array $items, bool $checkRepeated = true): void{
                foreach($items as $item){
                        if(!($item instanceof Item) || (self::hasItemId($item) && $checkRepeated)) continue;
                        $this->backupItem($player, $item);
                }
        }

        /**
         * @param      $player
         * @param Item $item
         */
        public function backupItem($player, Item $item): void{
                RecoveryModel::query()->create(["name" => RandomUtils::getName($player), "item_id" => self::getItemId($item), "item_data" => json_encode($item->jsonSerialize())]);
        }

        /**
         * @param $player
         *
         * @return Item[]
         */
        public function fetchItems($player): array{
                $selection = RecoveryModel::query()->where("name", "=", RandomUtils::getName($player))->get()->toArray();
                return array_map(function (array $result){
                        $item = Item::jsonDeserialize(json_decode($result["item_data"], true));
                        $lore = $item->getLore();

                        $lore []= RandomUtils::colorMessage("&r&l&8» &r&7ITEM RECOVERY");
                        $lore []= RandomUtils::colorMessage("&r&l&8» &r&7lost id: &f" . $result["id"]);
                        $lore []= RandomUtils::colorMessage("&r&l&8» &r&7lost date: &f" . date("m/d/y g:i A", strtotime($result["created_at"])));
                        $lore []= RandomUtils::colorMessage("&r&l&8» &r&7recovery date: &f" . date("m/d/y g:i A"));
                        $lore []= RandomUtils::colorMessage("&r&l&8» &r&7item uuid: &f" . $result["item_id"]);

                        return $item->setLore($lore);
                }, $selection);
        }

        /**
         * @param Player $player
         * @param string $username
         *
         * @return bool
         */
        public function openRecoveryInventory(Player $player, string $username): bool{
                if(!isset($this->viewing[spl_object_hash($player)])){
                        if(count($items = $this->fetchItems($username)) <= 0) return false;
                        $this->viewing[spl_object_hash($player)] = array_chunk($items, self::PAGE_SIZE);
                }

                $inventory = $this->core->module_loader->inventory_menu->openDoubleChestInventory($player, function (TransactionData $data){
                        $player = $data->player;

                        if($data->slot !== self::LAST_PAGE_INDEX && $data->slot !== self::NEXT_PAGE_INDEX){
                                $player->getInventory()->addItem($data->source_item);
                        }

                        $data->inventory->clearAll();

                        $viewing = $this->viewing[spl_object_hash($player)];
                        $page = $this->page[spl_object_hash($player)] = $this->page[spl_object_hash($player)] ?? 0;

                        if($data->slot === self::LAST_PAGE_INDEX && $page > 0) --$page;
                        if($data->slot === self::NEXT_PAGE_INDEX && isset($viewing[$page + 1])) ++$page;

                        $item = VanillaBlocks::REDSTONE()->asItem();
                        $item->setCustomName(RandomUtils::colorMessage(" &r&l&aLAST PAGE (&2" . ($page + 1) . " &aof &2" . count($viewing) . "&a)"));
                        $data->inventory->setItem(self::LAST_PAGE_INDEX, $item);

                        $item = VanillaBlocks::EMERALD()->asItem();
                        $item->setCustomName(RandomUtils::colorMessage("&r&l&aNEXT PAGE (&2" . ($page + 1) . " &aof &2" . count($viewing) . "&a) "));
                        $data->inventory->setItem(self::NEXT_PAGE_INDEX, $item);

                        $items = $viewing[$page];
                        foreach($items as $item) $data->inventory->addItem($item);

                        $this->page[spl_object_hash($player)] = $page;
                        return true;
                }, [
                    "title" => RandomUtils::colorMessage("&8{$username}'s LOST ITEMS"),
                    "height" => 3,
                ]);

                $this->core->module_loader->inventory_menu->setCloseCallback($player, function () use ($player){
                        unset($this->viewing[spl_object_hash($player)], $this->page[spl_object_hash($player)]);
                });

                $viewing = $this->viewing[spl_object_hash($player)];

                $lastPageItem = VanillaBlocks::REDSTONE()->asItem();
                $lastPageItem->setCustomName(RandomUtils::colorMessage(" &r&l&aLAST PAGE (&21 &aof &2" . count($viewing) . "&a)"));

                $nextPageItem = VanillaBlocks::EMERALD()->asItem();
                $nextPageItem->setCustomName(RandomUtils::colorMessage("&r&l&aNEXT PAGE (&21 &aof &2" . count($viewing) . "&a) "));

                $inventory->setItem(self::LAST_PAGE_INDEX, $lastPageItem);
                $inventory->setItem(self::NEXT_PAGE_INDEX, $nextPageItem);

                foreach($viewing[0] as $item) $inventory->addItem($item);
                return true;
        }
}