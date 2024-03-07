<?php
namespace PrestigeSociety\CustomItems;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\Tag;
use pocketmine\player\Player;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\ConsoleUtils;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\InventoryMenu\TransactionData;
class CustomItems{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /** @var Item[] */
        protected array $item_cache = [];

        /**
         * CustomItems constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
                $this->core->getServer()->getPluginManager()->registerEvents(new EventListener($core), $core);
        }

        /**
         * @param Player $player
         * @param Item   $item
         *
         * @return bool
         */
        public function onUse(Player $player, Item $item): bool{
                $output = $this->claimCommands($player, $item);
                $output = $this->claimInventory($player, $item);

                return $output;
        }

        /**
         * @param string $name
         * @param int    $count
         * @param        $dataValue
         *
         * @return null|Item
         */
        public function getCustomItem(string $name, int $count, $dataValue): ?Item{
                $data = $this->core->module_configurations->custom_items[$name] ?? null;
                if($data !== null){
                        $class = "pocketmine\\nbt\\tag\\" . $data["tag_type"];

                        $item = RandomUtils::parseItemsWithEnchantments([$data["item"]])[0];
                        if($item->hasCustomName()){
                                $item->setCustomName(str_replace("@value", $dataValue, $item->getCustomName()));
                        }

                        $item->getNamedTag()->setString("claim_type", new StringTag($data["claim_type"]));
                        $item->getNamedTag()->setTag($data["tag"], new $class($dataValue));
                        $item->getNamedTag()->setTag("ench", new ListTag());;

                        $item->setCount($count);
                        return $item;
                }
                return null;
        }

        /**
         * @param Player $player
         * @param string $name
         * @param int    $count
         * @param        $dataValue
         *
         * @return bool
         */
        public function addCustomItem(Player $player, string $name, int $count, $dataValue): bool{
                $item = $this->getCustomItem($name, $count, $dataValue);
                if($item !== null){
                        $player->getInventory()->addItem($item);
                        return true;
                }
                return false;
        }

        /**
         * @param Tag $namedTag
         *
         * @return string
         */
        public function getTagName(Tag $namedTag): string{
                try{
                        return (new \ReflectionClass($namedTag))->getShortName();
                }catch(\ReflectionException $e){
                        return "";
                }
        }

        /**
         * @internal
         *
         * @param Player $player
         * @param Item   $item
         *
         * @return bool
         */
        protected function claimCommands(Player $player, Item $item): bool{
                $output = false;

                $data = $this->core->module_configurations->custom_items;
                foreach($data as $name => $datum){
                        $this->item_cache[$name] = $item_check = $this->item_cache[$name] ?? RandomUtils::parseItemsWithEnchantments([$datum["item"]])[0];
                        if($item_check->equals($item, true, false)){
                                $tag = $item->getNamedTag()->getTag($datum["tag"]);
                                if($tag !== null){
                                        $tag_type = $this->getTagName($tag);
                                        if(($datum["tag_type"] === "any" || $datum["tag_type"] === $tag_type) && ($datum["value"] === "any" || $datum["value"] == $tag->getValue())){
                                                if($datum["claim_type"] === "commands"){
                                                        foreach($datum["commands"] as $command){
                                                                $command = str_replace(["@player", "@value"], [$player->getName(), $tag->getValue()], $command);
                                                                ConsoleUtils::dispatchCommandAsConsole($command);
                                                        }
                                                        $player->sendMessage(RandomUtils::colorMessage($datum["message"]));
                                                        $player->getInventory()->removeItem($item->setCount(1));
                                                }

                                                $output = true;
                                        }
                                }
                        }
                }
                return $output;
        }

        /**
         * @internal
         *
         * @param Player $player
         * @param Item   $item
         *
         * @return bool
         */
        protected function claimInventory(Player $player, Item $item): bool{
                $output = false;

                $data = $this->core->module_configurations->custom_items;
                foreach($data as $name => $datum){
                        $this->item_cache[$name] = $item_check = $this->item_cache[$name] ?? RandomUtils::parseItemsWithEnchantments([$datum["item"]])[0];
                        if($item_check->equals($item, true, false)){
                                $tag = $item->getNamedTag()->getTag($datum["tag"]);
                                if($tag !== null){
                                        $tag_type = $this->getTagName($tag);
                                        if(($datum["tag_type"] === "any" || $datum["tag_type"] === $tag_type) && $datum["value"] === "any" || $datum["value"] == $tag->getValue()){
                                                if($datum["claim_type"] === "inventory"){
                                                        $inventory_data = [];

                                                        foreach($datum["inventory"] as $inventory_value){
                                                                $splits = explode("$", $inventory_value);

                                                                $execute_type = $splits[0];
                                                                $index = (int) $splits[1];

                                                                if($execute_type === "i"){
                                                                        $inventory_data[$index] = [
                                                                            $execute_type,
                                                                            RandomUtils::parseItemsWithEnchantments([$splits[2]])[0],
                                                                        ];
                                                                }elseif($execute_type === "cmd"){
                                                                        $inventory_data[$index] = [
                                                                            $execute_type,
                                                                            RandomUtils::parseItemsWithEnchantments([$splits[2]])[0],
                                                                            str_replace(["@player", "@value"], [$player->getName(), $tag->getValue()], $splits[3])
                                                                        ];
                                                                }
                                                        }

                                                        $chest_inventory = $this->core->module_loader->inventory_menu->openInventory($player, function (TransactionData $data) use ($inventory_data, $datum, $item){
                                                                $player = $data->player;
                                                                $slot = $data->slot;

                                                                $item_data = $inventory_data[$slot] ?? null;
                                                                if($item_data !== null){
                                                                        $player->sendMessage(RandomUtils::colorMessage($datum["message"]));
                                                                        $player->getInventory()->removeItem($item->setCount(1));

                                                                        switch($item_data[0]){
                                                                                case "i":
                                                                                        $player->getInventory()->addItem($item_data[1]);
                                                                                        break;
                                                                                case "cmd":
                                                                                        ConsoleUtils::dispatchCommandAsConsole($item_data[2]);
                                                                                        break;
                                                                        }
                                                                        $this->core->module_loader->inventory_menu->closeInventory($player);
                                                                }
                                                                return true;
                                                        }, [
                                                            "height" => 5,
                                                            "title" => RandomUtils::colorMessage($datum["inventory_title"]),
                                                        ]);

                                                        $this->core->module_loader->inventory_menu->setDropItemCallback($player, function ($player, Item $dropped_item) use ($item){
                                                                return $dropped_item->equals($item);
                                                        });

                                                        for($i = 0; $i < $chest_inventory->getSize(); $i++){
                                                                $item = $inventory_data[$i][1] ?? VanillaBlocks::COBWEB()->asItem();
                                                                $chest_inventory->setItem($i, $item);
                                                        }
                                                }

                                                $output = true;
                                        }
                                }
                        }
                }
                return $output;
        }
}