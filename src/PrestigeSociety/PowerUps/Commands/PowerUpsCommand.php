<?php

namespace PrestigeSociety\PowerUps\Commands;
use pocketmine\command\CommandSender;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class PowerUpsCommand extends CoreCommand{
        /**
         * PowerUpsCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                parent::__construct($plugin, "powerups", "Buy power-ups!", RandomUtils::colorMessage("&e/powerups"), []);
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

                $options = ["check_permissions" => true, "message" => $this->core->getMessage("command_lock", "power_ups")];
                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->power_ups->POWER_UPS_ID, $sender, [], $options);
                return true;
        }
}
