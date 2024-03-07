<?php
namespace PrestigeSociety\Core\Commands;
use pocketmine\utils\TextFormat;
use PrestigeSociety\Core\PrestigeSocietyCore;
use pocketmine\command\CommandSender;
class SetOnFireCommand extends CoreCommand{
        /**
         * SetOnFireCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                $this->setPermission("command.setonfire");
                parent::__construct($plugin, "setonfire", "set a player on fire", "Usage: /setonfire <player> <seconds>", ["setfire"]);
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

                if(count($args) < 2){
                        $this->core->sendMessage($sender, $this->getUsage());
                        return false;
                }

                if(!is_numeric($args[1])){
                        $this->core->sendMessage($sender, TextFormat::RED . "Seconds must be a number");
                        $this->core->sendMessage($sender, TextFormat::YELLOW . $this->getUsage());

                        return false;
                }

                $player = $args[0];
                $seconds = (int) $args[1];

                $player = $sender->getServer()->getPlayerByPrefix($player);
                if($player === null){
                        $this->core->sendMessage($sender, TextFormat::RED . "Player is offline.");
                        return false;
                }

                $player->setOnFire($seconds);
                $this->core->sendMessage($sender, TextFormat::GREEN . "{$player->getName()} has been set on fire for $seconds seconds.");

                return true;
        }
}