<?php
namespace PrestigeSociety\Core\Commands;
use pocketmine\player\Player;
use PrestigeSociety\Core\PrestigeSocietyCore;
use pocketmine\command\CommandSender;
use PrestigeSociety\PowerUps\PowerUps;
class FlyCommand extends CoreCommand{
        /**
         * FlyCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                $this->setPermission("command.fly");
                parent::__construct($plugin, "fly", "Allows you to fly", "/fly", ["flight"]);
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

                if(!$this->core->module_loader->power_ups->isPowerUpActive($sender, PowerUps::POWER_UP_FLIGHT)){
                        if(!$this->testPermission($sender)){
                                return false;
                        }
                }

                $this->core->module_loader->fun_box->toggleFlight($sender);
                return true;
        }
}