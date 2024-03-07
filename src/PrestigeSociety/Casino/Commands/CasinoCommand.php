<?php

namespace PrestigeSociety\Casino\Commands;
use pocketmine\command\CommandSender;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class CasinoCommand extends CoreCommand{
        /**
         * CasinoCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                parent::__construct($plugin, "casino", "Access the casino!", RandomUtils::colorMessage("&e/casino"), []);
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

                $options = ["check_permissions" => true, "message" => $this->core->getMessage("command_lock", "casino")];
                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->casino->CASINO_ID, $sender, [], $options);
                return true;
        }
}
