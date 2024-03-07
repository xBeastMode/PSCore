<?php

namespace PrestigeSociety\Nicknames;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class NickCommand extends CoreCommand {
        /**
         * NickCommand constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                parent::__construct($core, "nick", "Set a fancy nickname!", RandomUtils::colorMessage("&eUsage: /nick <nick>"), ["nickname"]);
                $this->setPermission("command.nick");
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

                if(count($args) < 1){
                        $sender->sendMessage($this->getUsage());
                        return false;
                }

                $nick = $args[0];

                if($nick === "reset"){
                        if($this->core->module_loader->nicknames->hasNick($sender)){
                                $this->core->module_loader->nicknames->resetNick($sender);
                                //$sender->setDisplayName($this->core->moduleLoader->chat->formatDisplayName($sender));
                                $sender->setNameTag($this->core->module_loader->chat->formatDisplayName($sender));
                                $message = $this->core->getMessage("nicks", "reset_nick");
                                $sender->sendMessage(RandomUtils::colorMessage($message));
                        }else{
                                $message = $this->core->getMessage("nicks", "cannot_reset");
                                $sender->sendMessage(RandomUtils::colorMessage($message));
                        }
                        return false;
                }

                $this->core->module_loader->nicknames->resetNick($sender);
                $this->core->module_loader->nicknames->setNick($sender, RandomUtils::colorMessage($nick));
                //$sender->setDisplayName($this->core->moduleLoader->chat->formatDisplayName($sender));
                $sender->setNameTag($this->core->module_loader->chat->formatDisplayName($sender));
                $message = $this->core->getMessage("nicks", "set_nick");
                $message = str_replace("@nick", RandomUtils::colorMessage($nick), $message);
                $sender->sendMessage(RandomUtils::colorMessage($message));

                return true;
        }
}