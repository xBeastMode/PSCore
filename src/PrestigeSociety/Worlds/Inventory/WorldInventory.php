<?php
namespace PrestigeSociety\Worlds\Inventory;
use pocketmine\item\Item;
use pocketmine\player\Player;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\DataModels\InventoriesModel;
class WorldInventory{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /** @var WorldInventory|null */
        public static ?WorldInventory $instance = null;

        /**
         * Worlds constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;

                self::$instance = $this;
        }

        /**
         * @param Player $player
         * @param string $world
         */
        public function equipInventory(Player $player, string $world): void{
                $inventory = $this->getPlayerInventory($player, $world);

                $player->getInventory()->setContents($inventory[0]);
                $player->getArmorInventory()->setContents($inventory[1]);
        }

        /**
         * @param Player $player
         * @param string $world
         *
         * @return array
         */
        public function getPlayerInventory(Player $player, string $world): array{
                $username = $player->getName();

                $record = InventoriesModel::query()->where([["name", "=", $username], ["world", "=", $world]]);
                if($record->exists()){
                        $output = [[], []];

                        $items = json_decode($record->value("inventory"), true);
                        foreach($items[0] as $index => $item){
                                $output[0][$index] = Item::jsonDeserialize($item);
                        }
                        foreach($items[1] as $index => $item){
                                $output[1][$index] = Item::jsonDeserialize($item);
                        }

                        return $output;
                }

                return [[], []];
        }

        /**
         * @param Player $player
         * @param string $world
         */
        public function savePlayerInventory(Player $player, string $world): void{
                $output = [];
                foreach($player->getInventory()->getContents(true) as $index => $item){
                        $output[0][$index] = $item->jsonSerialize();
                }
                foreach($player->getArmorInventory()->getContents(true) as $index => $item){
                        $output[1][$index] = $item->jsonSerialize();
                }
                $output = json_encode($output);
                $username = $player->getName();

                InventoriesModel::query()->updateOrCreate(["name" => $username, "world" => $world], ["inventory" => $output]);
        }
}