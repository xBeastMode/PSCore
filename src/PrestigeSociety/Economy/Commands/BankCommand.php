<?php

namespace PrestigeSociety\Economy\Commands;
use pocketmine\command\CommandSender;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
class BankCommand extends CoreCommand{
        /**
         * BankCommand constructor.
         * 
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                parent::__construct($plugin, "bank", "Manage your bank account", "Usage: /bank", []);
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

                $options = ["check_permissions" => true, "message" => $this->core->getMessage("command_lock", "bank")];
                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->economy->BANK_ID, $sender, [], $options);
                return true;
        }
}