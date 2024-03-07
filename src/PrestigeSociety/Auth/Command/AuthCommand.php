<?php
namespace PrestigeSociety\Auth\Command;
use pocketmine\command\CommandSender;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use pocketmine\plugin\Plugin;
class AuthCommand extends CoreCommand{
        /**
         * AuthCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                parent::__construct($plugin, "auth", "Check auth commands", RandomUtils::colorMessage("&e/auth help"), ["spa"]);
        }

        /**
         * @param CommandSender $sender
         * @param string        $commandLabel
         * @param string[]      $args
         *
         * @return bool
         */
        public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
                if(empty($args)){
                        $sender->sendMessage($this->getUsage());
                        return false;
                }
                switch(strtolower($args[0])){
                        case "help":
                                if(!$sender->isOp()){
                                        $sender->sendMessage(RandomUtils::colorMessage("&c--=[&dPrestigeSociety &fAuth&r&c]=--"));
                                        $sender->sendMessage(RandomUtils::colorMessage("&e/auth reset <old> <new> : &fchanges your password"));
                                }else{
                                        $sender->sendMessage(RandomUtils::colorMessage("&c--=[&dPrestigeSociety &fAuth&r&c]=--"));
                                        $sender->sendMessage(RandomUtils::colorMessage("&e/auth reset <old> <new> : &fchanges your password"));
                                        //$sender->sendMessage(RandomUtils::colorMessage("&e/auth admin help : &fshows auth commands for admins"));
                                }
                                break;
                        case "reset":
                        case "chngpwd":
                        case "change-password":
                        case "chpwd":
                                if(!$sender instanceof Player) return false;
                                if($this->core->module_loader->auth->changePassword($sender, $args[1], $args[2])){
                                        $sender->sendMessage(RandomUtils::colorMessage($this->core->getMessage("auth", "change_password_success")));
                                }else{
                                        $sender->sendMessage(RandomUtils::colorMessage($this->core->getMessage("auth", "old_password_not_match")));
                                }
                                break;
                        /*case "admin":
                                if($sender->isOp() or $sender->hasPermission("auth.Commands.admin")){
                                        switch(strtolower($args[1])){
                                                case "help":
                                                        $sender->sendMessage(RandomUtils::colorMessage("&c--=[&dPrestigeSociety &fAuth&r&c]=--"));
                                                        $sender->sendMessage(RandomUtils::colorMessage("&e/auth admin reset <player> : &fresets player auth account"));
                                                        $sender->sendMessage(RandomUtils::colorMessage("&e/auth admin change <player> <new> : &fchanges player password"));
                                                        $sender->sendMessage(RandomUtils::colorMessage("&e/auth admin register <player> <password> <confirm_password> : &fregisters an account"));
                                                        break;
                                                case "reset":
                                                        if(empty($args[2])){
                                                                $sender->sendMessage(RandomUtils::colorMessage($this->c->getMessage("auth", "enter_player_name")));
                                                        }else{

                                                        }
                                                        break;
                                        }
                                }
                                break;*/
                }
                return false;
        }

        /**
         * 
         * @return PrestigeSocietyCore
         * 
         */
        public function getPlugin(): Plugin{
                return $this->core;
        }
}