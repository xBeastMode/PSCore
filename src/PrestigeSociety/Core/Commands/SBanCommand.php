<?php
namespace PrestigeSociety\Core\Commands;
use Exception;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use PrestigeSociety\Core\PrestigeSocietyCore;
use pocketmine\command\CommandSender;
use PrestigeSociety\Core\Utils\StringUtils;
use PrestigeSociety\Core\Utils\RandomUtils;
class SBanCommand extends CoreCommand{
        /**
         * SBanCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                $this->setPermission("command.sban");
                parent::__construct($plugin, "sban", "Silent ban a player", "Usage: /sban <player> [time] [reason...]", ["ban"]);
        }

        /**
         * @param CommandSender $sender
         * @param string        $commandLabel
         * @param string[]      $args
         *
         * @return bool
         *
         * @throws Exception
         */
        public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
                if(!$this->testPermission($sender)){
                        return false;
                }

                if(count($args) <= 0){
                        $sender->sendMessage(TextFormat::RED . $this->getUsage());
                        return false;
                }

                $name = array_shift($args);
                $player = $this->core->getServer()->getPlayerByPrefix($name);

                $time = $args[0] ?? -1;
                $time = is_numeric($time) ? (int)$time : $time;

                $timestamp = null;

                if(is_string($time) && !($timestamp = StringUtils::stringToTimestamp(implode(" ", $args)))){
                        $sender->sendMessage(RandomUtils::colorMessage("&l&4[!] &cPlease specify a valid time."));
                        return false;
                }

                array_shift($args); # shift time argument, leave reason
                $reason = count($args) > 0 ? implode(" ", $args) : "";

                $name = $player instanceof Player ? $player->getName() : $name;
                $message = "&l&4[!] " . $name . " &chas been banned by &4" . $sender->getName() . "&c.";

                if($time === -1){
                        $message .= $timestamp !== null ? " Time: &4forever&c." : "";
                        $message .= $reason !== "" ? " Reason: &4$reason&c." : "";

                        $sender->getServer()->broadcastMessage(RandomUtils::colorMessage($message));
                        $this->core->getServer()->getNameBans()->addBan($name, $reason, null, $sender->getName());

                        if($player instanceof Player){
                                $player->kick(RandomUtils::colorMessage($message), false);
                        }
                        return true;
                }

                if($player instanceof Player && $player->hasPermission("ban.bypass")){
                        $sender->sendMessage(RandomUtils::colorMessage("&l&4[!] $name &ccan't be banned."));
                        return false;
                }

                /** @var \DateTime $date */
                $date = $timestamp[0];
                $reason = $timestamp[1];

                //$date->format("l, F j, Y")

                $diff = $date->diff(new \DateTime());
                $times = [];

                $stamps = [
                    "y" => "years",
                    "m" => "months",
                    "d" => "days",
                    "h" => "hours",
                    "i" => "minutes",
                    "s" => "seconds"
                ];

                foreach($stamps as $char => $stamp){
                        if($diff->{$char} > 0){
                                $times[] = "&4" . $diff->{$char} . " &c" . $stamp;
                        }
                }

                $message .= $timestamp !== null ? " Time: &4" . implode(", ", $times) . "." : "";
                $message .= $reason !== "" ? " Reason: &4$reason&f." : "";

                $sender->sendMessage(RandomUtils::colorMessage($message));
                $sender->getServer()->getNameBans()->addBan($name, (trim($reason) !== "" ? $reason : ""), $date, $sender->getName());

                if($player instanceof Player){
                        $player->kick(RandomUtils::colorMessage($message), false);
                }

                return true;
        }
}