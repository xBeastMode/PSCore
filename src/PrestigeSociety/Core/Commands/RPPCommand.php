<?php
namespace PrestigeSociety\Core\Commands;
use pocketmine\utils\TextFormat;
use PrestigeSociety\Core\PrestigeSocietyCore;
use pocketmine\command\CommandSender;
class RPPCommand extends CoreCommand{
        /**
         * RPPCommand constructor.
         * 
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                $this->setPermission("command.rpp");
                parent::__construct($plugin, "rpp", "Reload chat formats", "Usage: /rpp", ["rgroups"]);
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

                $this->core->reloadGroupsConfig();
                $this->core->pruneGroupsConfig();
                $sender->sendMessage(TextFormat::GREEN . "Reloaded chat formats.");

                return true;
        }
}