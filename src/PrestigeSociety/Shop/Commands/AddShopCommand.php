<?php

namespace PrestigeSociety\Shop\Commands;
use pocketmine\command\CommandSender;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
class AddShopCommand extends CoreCommand{
        /**
         * AddShopCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                parent::__construct($plugin, "addshop", "Add a shop item", "Usage: /addshop", []);
                $this->setPermission("command.addshop");
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
                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->shop->SELECT_CATEGORY_ID, $sender, ["action" => 0]);
                return true;
        }
}