<?php

namespace PrestigeSociety\Core\Commands;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;

class GodCommand extends CoreCommand{
        /**
         * GodCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                parent::__construct($plugin, "god", "Become invincible!", RandomUtils::colorMessage("&e/god"), []);
                $this->setPermission("command.god");
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

                $this->core->module_loader->fun_box->toggleGod($sender);
                return true;
        }
}