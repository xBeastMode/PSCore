<?php

namespace PrestigeSociety\Ranks\Commands;
use pocketmine\command\CommandSender;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class RankUpCommand extends CoreCommand{
        /**
         * RankUpCommand constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                parent::__construct($core, "rankup", "Try a rank up!", "Usage: /rankup", []);
                $this->setPermission("command.rankup");
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

                $result = $this->core->module_loader->ranks->rankUp($sender);

                $message = '';

                if($result === 2){
                        $message = 'non_sufficient_funds';
                }elseif($result === 1){
                        $message = 'already_highest_rank';
                }elseif($result === 0){
                        $rank = $this->core->module_loader->ranks->getRank($sender);
                        $message = $this->core->getMessage('ranks', 'ranked_up');
                        $message = str_replace(["@player", "@rank"], [$sender->getName(), $rank], $message);
                        $sender->getServer()->broadcastMessage(RandomUtils::colorMessage($message));
                        return false;
                }

                $price = $this->core->module_loader->ranks->getNextRankPrice($sender) * $this->core->module_loader->levels->getLevel($sender);

                $message = $this->core->getMessage('ranks', $message);
                $message = str_replace("@rank_up_price", $price, $message);
                $sender->sendMessage(RandomUtils::colorMessage($message));

                return true;
        }
}