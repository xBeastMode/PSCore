<?php

namespace PrestigeSociety\Statistics\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Statistics\StatisticsListener;
class StatCommand extends CoreCommand{
        /**
         * StatCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                $this->setPermission("command.statistic");
                parent::__construct($plugin, "statistic", "Statistics command", RandomUtils::colorMessage("&eUsage: /stat <type> <place>"), ["stat"]);
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

                if(count($args) === 1 && $args[0] === "remove"){
                        StatisticsListener::$sessions[spl_object_hash($sender)] = true;
                        $sender->sendMessage(RandomUtils::colorMessage("&aHit stat human to remove it."));
                        return false;
                }
                if(count($args) < 2){
                        $sender->sendMessage(RandomUtils::colorMessage($this->getUsage()));
                        return false;
                }

                if(!in_array($args[0], ["kills", "deaths", "money", "levels", "play_time", "bosses_killed"])){
                        $sender->sendMessage(RandomUtils::colorMessage("&cInvalid type, must be: kills, deaths, money, levels, play_time, bosses_killed."));
                        return false;
                }

                if(!is_numeric($args[1])){
                        $sender->sendMessage(RandomUtils::colorMessage("&cInvalid place number. The place number must be an integer."));
                        return false;
                }

                $type = $args[0];
                $place = (int)$args[1];

                $this->core->module_loader->statistics->addNew($sender, $type, $place);
                $sender->sendMessage(RandomUtils::colorMessage("&aAdded " . $type . " stats for place " . $place . "."));
                return true;
        }
}