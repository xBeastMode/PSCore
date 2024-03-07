<?php
namespace PrestigeSociety\Core\Commands;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class UnmuteCommand extends CoreCommand{
        /**
         * UnmuteCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                $this->setPermission("command.unmute");
                parent::__construct($plugin, "unmute", "Un-mute a player", RandomUtils::colorMessage("&eUsage: /unmute <player>"), []);
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

                /** @var Player $sender */

                if(count($args) > 0){
                        $player = $sender->getServer()->getPlayerByPrefix($args[0]);
                        $mod = $this->core->module_loader->chat;

                        if($player === null){
                                if(!$mod->unMuteOfflinePlayer($args[0])){
                                        $sender->sendMessage(TextFormat::GREEN . "Could not un-mute that player.");
                                        return false;
                                }
                                $sender->sendMessage(TextFormat::GREEN . "Successfully unmuted {$args[0]}.");
                        }else{
                                if(!$mod->unMutePlayer($player)){
                                        $sender->sendMessage(TextFormat::GREEN . "Could not un-mute that player.");
                                        return false;
                                }
                                $msg = RandomUtils::colorMessage($this->core->getMessage("chat_protector", "unmuted"));
                                $player->sendMessage($msg);

                                $sender->sendMessage(TextFormat::GREEN . "Successfully unmuted {$player->getName()}.");
                        }
                }else{
                        $sender->sendMessage($this->getUsage());
                }

                return true;
        }
}