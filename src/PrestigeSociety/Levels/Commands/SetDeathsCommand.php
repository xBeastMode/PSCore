<?php
namespace PrestigeSociety\Levels\Commands;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use pocketmine\command\CommandSender;
class SetDeathsCommand extends CoreCommand{
        /**
         * SetDeathsCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                $this->setPermission("command.setdeaths");
                parent::__construct($plugin, "setdeaths", "Allows you to set a player's deaths", "/setdeaths <player> <deaths>", []);
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

                $deaths = intval($args[1]);

                if(!is_numeric($deaths) or !is_int($deaths)){
                        $sender->sendMessage(RandomUtils::colorMessage("The deaths amount must be an integer."));
                        return false;
                }

                if($deaths < 0){
                        $sender->sendMessage(RandomUtils::colorMessage("The deaths amount cannot be less than 0."));
                        return false;
                }

                $player = $args[0];
                $this->core->module_loader->levels->setDeaths($player, $deaths);
                $sender->sendMessage(RandomUtils::colorMessage(str_replace(["@player", "@deaths"], [$player, $deaths], $this->core->getMessage("levels", "player_deaths_set"))));

                return true;
        }
}