<?php

namespace PrestigeSociety\Ranks\Commands;
use pocketmine\command\CommandSender;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class SeeRankCommand extends CoreCommand{
        /**
         * SeeRankCommand constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                parent::__construct($core, "seerank", "See another player's rank!", RandomUtils::colorMessage("&eUsage: /seerank <player>"), ["viewrank", "vrank"]);
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
                        $sender->sendMessage($this->getUsage());
                        return false;
                }

                $rank = $this->core->module_loader->ranks->getRank($args[0]);

                if($rank === null){
                        $message = $this->core->getMessage('ranks', 'player_not_found');
                        $sender->sendMessage(RandomUtils::colorMessage($message));
                        return false;
                }

                $message = $this->core->getMessage('ranks', 'player_rank');
                $message = str_replace(["@rank", "@player"], [$rank, $args[0]], $message);
                $sender->sendMessage(RandomUtils::colorMessage($message));

                return true;
        }
}