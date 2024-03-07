<?php

namespace PrestigeSociety\Core\Commands;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class ToggleCommand extends CoreCommand{
        /**
         * ToggleCommand constructor.
         * 
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                parent::__construct($plugin, "toggle", "toggle effects on players", RandomUtils::colorMessage("&eUsage: /toggle <player> <enable|disable|toggle> <flight|lsd|god>"), []);
                $this->setPermission("command.toggle");
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

                if(count($args) < 3){
                        $sender->sendMessage($this->getUsage());
                        return false;
                }

                $player = $sender->getServer()->getPlayerByPrefix($args[0]);
                if($player === null){
                        $sender->sendMessage(TextFormat::RED . "Player is offline.");
                        return false;
                }

                $mode = strtolower($args[1]);
                $effect = strtolower($args[2]);

                $effects = ["flight", "lsd", "god"];
                $modes = ["enable", "disable", "toggle"];

                if(!in_array($effect, $effects)){
                        $sender->sendMessage(TextFormat::RED . "Unknown effect. It must be: " . implode(", ", $effects));
                        return false;
                }

                if(!in_array($mode, $modes)){
                        $sender->sendMessage(TextFormat::RED . "Unknown mode. It must be: " . implode(", ", $modes));
                        return false;
                }

                $functions = [
                    "enable" => [
                        "flight" => "enableFlight",
                        "lsd" => "enableLSD",
                        "god" => "enableGod",
                    ],
                    "disable" => [
                        "flight" => "disableFlight",
                        "lsd" => "disableLSD",
                        "god" => "disableGod",
                    ],
                    "toggle" => [
                        "flight" => "toggleFlight",
                        "lsd" => "toggleLSD",
                        "god" => "toggleGod",
                    ],
                ];

                $func = $functions[$mode][$effect];
                $this->core->module_loader->fun_box->$func($player);
                $sender->sendMessage(TextFormat::GREEN . "$effect {$mode}d for {$player->getName()}");

                return true;
        }
}