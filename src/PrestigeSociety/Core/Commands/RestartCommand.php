<?php

namespace PrestigeSociety\Core\Commands;
use pocketmine\command\CommandSender;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class RestartCommand extends CoreCommand{
        /**
         * RestartCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                parent::__construct($plugin, "restart", "Check when the server will restart", RandomUtils::colorMessage("&eUsage: /restart"), []);
                $this->setPermission("command.restart");
        }

        /**
         * @param CommandSender $sender
         * @param string        $commandLabel
         * @param string[]      $args
         *
         * @return bool
         */
        public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
                if(!$this->testPermission($sender)){
                        return false;
                }
                $this->core->module_loader->restarter->setTime(10);
                return true;
        }
}