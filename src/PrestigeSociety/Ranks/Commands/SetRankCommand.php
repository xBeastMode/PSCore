<?php

namespace PrestigeSociety\Ranks\Commands;
use pocketmine\command\CommandSender;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class SetRankCommand extends CoreCommand{
        /**
         * SetRankCommand constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                parent::__construct($core, "setprank", "Set a player's rank!", RandomUtils::colorMessage("&eUsage: /setprank <player> <rank>"), []);
                $this->setPermission("command.setrank");
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
                        $sender->sendMessage($this->getUsage());
                        return false;
                }

                $player = $args[0];
                $rank = $args[1];

                $res = $this->core->module_loader->ranks->setRank($player, $rank);

                if($res){
                        $message = $this->core->getMessage('ranks', 'set_rank');
                        $message = str_replace(["@player", "@rank"], [$player, $rank], $message);
                        $sender->sendMessage(RandomUtils::colorMessage($message));
                }else{
                        $message = $this->core->getMessage('ranks', 'invalid_rank_name');
                        $sender->sendMessage(RandomUtils::colorMessage($message));
                }

                return true;
        }
}