<?php

namespace PrestigeSociety\Credits\Commands;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\StringUtils;
use PrestigeSociety\Core\Utils\RandomUtils;
class SubtractCreditsCommand extends CoreCommand{
        /**
         * SubtractCreditsCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                $this->setPermission("credits.command.all;credits.command.subcredits");
                parent::__construct($plugin, "subtractcredits", "Subtract credits from player", "Usage: /subtractcredits <player> <amount>", ["subcredits", "takecredits"]);
        }


        /**
         *
         * @param CommandSender $sender
         * @param string        $commandLabel
         * @param string[]      $args
         *
         * @return bool
         */
        public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
                if(count($args) < 2){
                        $sender->sendMessage(TextFormat::GREEN . $this->getUsage());
                        return false;
                }

                $player = $args[0];
                if(StringUtils::checkIsNumber($credits = $args[1])){
                        if(!$this->core->module_loader->credits->playerExists($player)){
                                $message = $this->core->getMessage("credits", "no_player");
                                $sender->sendMessage(RandomUtils::colorMessage($message));
                                return false;
                        }
                        if($this->core->module_loader->credits->subtractCredits($player, $credits)){
                                $message = $this->core->getMessage("credits", "subtracted_credits");
                                $message = str_replace(["@player", "@credits"], [$player, $credits], $message);
                                $sender->sendMessage(RandomUtils::colorMessage($message));

                                if(($player = $sender->getServer()->getPlayerByPrefix($player)) !== null){
                                        $message = $this->core->getMessage("credits", "subtracted_credits_notification");
                                        $message = str_replace(["@player", "@credits"], [$sender->getName(), $credits], $message);
                                        $player->sendMessage(RandomUtils::colorMessage($message));
                                }
                        }else{
                                $message = $this->core->getMessage("credits", "too_little_credits");
                                $message = str_replace(["@player", "@credits", "@balance"], [$player, $credits, $this->core->module_loader->credits->getCredits($player)], $message);
                                $sender->sendMessage(RandomUtils::colorMessage($message));
                        }
                }else{
                        $message = $this->core->getMessage("credits", "non_numeric");
                        $sender->sendMessage(RandomUtils::colorMessage($message));
                }
                return true;
        }
}