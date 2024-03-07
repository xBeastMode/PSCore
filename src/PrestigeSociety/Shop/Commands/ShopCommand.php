<?php

namespace PrestigeSociety\Shop\Commands;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Forms\FormList\Shop\SelectItemForm;
class ShopCommand extends CoreCommand{
        /**
         * ShopCommand constructor.
         * 
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                parent::__construct($plugin, "shop", "Shop for items!", "Usage: /shop [category]", ["shop"]);
        }

        /**
         * @param string $category
         *
         * @return int
         */
        protected function categoryToId(string $category): int{
                $ids = [
                    "Armor", // 0
                    "Beds",
                    "Carpet",
                    "Common Blocks",
                    "Concrete",
                    "Decoration", // 5
                    "Doors/Gates",
                    "Farming",
                    "Fencing",
                    "Food",
                    "Gadgets", // 10
                    "Glass",
                    "Lighting",
                    "Minerals",
                    "Miscellaneous",
                    "Potions", // 15
                    "Slabs",
                    "Terracotta",
                    "Tools",
                    "Weapons",
                    "Wood",
                    "Wool",
                ];

                return array_flip($ids)[$category] ?? 0;
        }

        /**
         * @param CommandSender $sender
         * @param string        $commandLabel
         * @param string[]      $args
         *
         * @return bool
         */
        public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
                if(!$this->testPlayer($sender)){
                        return false;
                }

                /** @var Player $sender */

                $options = ["check_permissions" => true, "message" => $this->core->getMessage("command_lock", "shop")];

                if(count($args) < 1){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->shop->SELECT_CATEGORY_ID, $sender, ["action" => 2], $options);
                }else{
                        $category = $this->categoryToId($args[0]);
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->shop->SELECT_ITEM_ID, $sender, [
                            "action" => 2,
                            "category" => $category,
                            "idAction" => 1,
                            SelectItemForm::METADATA_NO_BACK,
                        ], $options);
                }
                return true;
        }
}