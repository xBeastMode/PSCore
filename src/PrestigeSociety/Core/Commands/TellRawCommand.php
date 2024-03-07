<?php
namespace PrestigeSociety\Core\Commands;
use pocketmine\command\CommandSender;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class TellRawCommand extends CoreCommand{
        /**
         * TellRawCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                $this->setPermission("command.tellraw");
                parent::__construct($plugin, "tellraw", "Send a raw message", "Usage: /tellraw <player> <message>", []);
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
                
                if(count($args) <= 2){
                        $sender->sendMessage($this->getUsage());
                        return false;
                }
                
                $player = array_shift($args);
                $message = implode(" ", $args);
                
                if(($player = $this->core->getServer()->getPlayerByPrefix($player)) === null){
                        $sender->sendMessage("$player is offline.");
                        return false;
                }
                
                $player->sendMessage(RandomUtils::colorMessage($message));
                return true;
        }
}