<?php

namespace PrestigeSociety\Credits\Commands;
use pocketmine\command\CommandSender;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class CreditsCommand extends CoreCommand{
        /**
         * CreditsCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                parent::__construct($plugin, "credits", "See credits of player or yourself", "Usage: /credits [player]", ["credits"]);
        }

        /**
         * @param CommandSender $sender
         * @param string        $commandLabel
         * @param string[]      $args
         *
         * @return bool
         */
        public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
                if(count($args) < 1){
                        $message = $this->core->getMessage("credits", "my_balance");
                        $message = str_replace("@credits", $this->core->module_loader->credits->getCredits($sender), $message);
                        $sender->sendMessage(RandomUtils::colorMessage($message));
                        return true;
                }else{
                        $player = $args[0];
                        if(!$this->core->module_loader->credits->playerExists($player)){
                                $message = $this->core->getMessage("credits", "no_player");
                                $sender->sendMessage(RandomUtils::colorMessage($message));
                                return false;
                        }
                        $message = $this->core->getMessage("credits", "player_balance");
                        $message = str_replace(["@player", "@credits"], [$player, $this->core->module_loader->credits->getCredits($player)], $message);
                        $sender->sendMessage(RandomUtils::colorMessage($message));
                }
                return true;
        }
}