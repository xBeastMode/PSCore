<?php
namespace PrestigeSociety\Levels\Commands;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use pocketmine\command\CommandSender;
class SetKillsCommand extends CoreCommand{
        /**
         * SetKillsCommand constructor.
         * 
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                $this->setPermission("command.setkills");
                parent::__construct($plugin, "setkills", "Allows you to set a player's kills", "/setkills <player> <kills>", []);
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

                $kills = intval($args[1]);
                if(!is_numeric($kills) or !is_int($kills)){
                        $sender->sendMessage(RandomUtils::colorMessage("The kills amount must be an integer."));
                        return false;
                }

                if($kills < 0){
                        $sender->sendMessage(RandomUtils::colorMessage("The kills amount cannot be less than 0."));
                        return false;
                }

                $player = $args[0];
                $this->core->module_loader->levels->setKills($player, $kills);
                $sender->sendMessage(RandomUtils::colorMessage(str_replace(["@player", "@kills"], [$player, $kills], $this->core->getMessage("levels", "player_kills_set"))));

                return true;
        }
}