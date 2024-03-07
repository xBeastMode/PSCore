<?php
namespace PrestigeSociety\Management\Command;
use pocketmine\command\CommandSender;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
class ManageCommand extends CoreCommand{
        /**
         * ManageCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                parent::__construct($plugin, "manage", "Manage held item", "Usage: /manage", []);
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
                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->management->MANAGEMENT_ID, $sender);
                return false;
        }
}