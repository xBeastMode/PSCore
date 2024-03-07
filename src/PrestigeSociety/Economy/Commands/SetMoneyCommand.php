<?php

namespace PrestigeSociety\Economy\Commands;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\StringUtils;
use PrestigeSociety\Core\Utils\RandomUtils;
class SetMoneyCommand extends CoreCommand{
        /**
         * SetMoneyCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                $this->setPermission("economy.command.all;economy.command.setmoney");
                parent::__construct($plugin, "setmoney", "Set a player's money", "Usage: /setmoney <player> <amount>", []);
        }

        /**
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

                if(!$this->testPermission($sender)){
                        return false;
                }

                $player = $args[0];
                if(StringUtils::checkIsNumber($money = $args[1])){
                        if(!$this->core->module_loader->economy->playerExists($player)){
                                $message = $this->core->getMessage("economy", "no_player");
                                $sender->sendMessage(RandomUtils::colorMessage($message));
                                return false;
                        }
                        $this->core->module_loader->economy->setMoney($player, $money);
                        $message = $this->core->getMessage("economy", "set_money");
                        $message = str_replace(["@player", "@money"], [$player, $money], $message);
                        $sender->sendMessage(RandomUtils::colorMessage($message));

                        if(($player = $sender->getServer()->getPlayerByPrefix($player)) !== null){
                                $message = $this->core->getMessage("economy", "set_money_notification");
                                $message = str_replace(["@player", "@money"], [$sender->getName(), $money], $message);
                                $player->sendMessage(RandomUtils::colorMessage($message));
                        }
                }else{
                        $message = $this->core->getMessage("economy", "non_numeric");
                        $sender->sendMessage(RandomUtils::colorMessage($message));
                }
                return true;
        }
}