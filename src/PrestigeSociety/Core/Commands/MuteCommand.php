<?php
namespace PrestigeSociety\Core\Commands;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class MuteCommand extends CoreCommand{
        /**
         * MuteCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                $this->setPermission("command.mute");
                parent::__construct($plugin, "mute", "Mute a player", RandomUtils::colorMessage("&eUsage: /mute <player> [seconds] [reason]"), []);
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

                /** @var Player $sender */

                if(count($args) > 0){
                        $player = $sender->getServer()->getPlayerExact($args[0]);
                        if($player === null){
                                $sender->sendMessage(TextFormat::RED . "$args[0] is offline.");
                                return false;
                        }

                        $seconds = $args[1] ?? 0;
                        $seconds = (int) $seconds;

                        $reason = "";
                        if(count($args) > 2){
                                array_shift($args);
                                array_shift($args);

                                $reason = implode(" ", $args);
                        }

                        if($this->core->module_loader->chat->mutePlayer($player, $seconds, $reason)){
                                $sender->sendMessage(TextFormat::GREEN . "Successfully muted {$player->getName()}, " . ($seconds > 0 ? $seconds . " seconds" : "until restart") . " for: " . ($reason !== "" ? $reason : "N/A"));

                                $time = $seconds <= 0 ? "until restart" : $seconds . " seconds";

                                $message = RandomUtils::colorMessage($this->core->getMessage("chat_protector", "muted"));
                                $message = str_replace(["@seconds", "@reason", "@time"], [$seconds, $reason !== "" ? $reason : "N/A", $time], $message);
                                $player->sendMessage($message);
                        }
                }else{
                        $sender->sendMessage($this->getUsage());
                }

                return true;
        }
}