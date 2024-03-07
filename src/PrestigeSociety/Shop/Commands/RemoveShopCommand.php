<?php

namespace PrestigeSociety\Shop\Commands;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
class RemoveShopCommand extends CoreCommand{
        /**
         * RemoveShopCommand constructor.
         * 
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                parent::__construct($plugin, "removeshop", "Remove a shop item", "Usage: /removeshop", []);
                $this->setPermission("command.removeshop");
        }

        /**
         * @param CommandSender $sender
         * @param string        $commandLabel
         * @param string[]      $args
         *
         * @return bool
         */
        public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
                if(!$this->testAll($sender)){
                        return false;
                }

                /** @var Player $sender */

                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->shop->SELECT_CATEGORY_ID, $sender, ["action" => 1]);
                return true;
        }
}