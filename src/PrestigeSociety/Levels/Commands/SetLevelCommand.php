<?php
namespace PrestigeSociety\Levels\Commands;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use pocketmine\command\CommandSender;
class SetLevelCommand extends CoreCommand{
        /**
         * SetLevelCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                $this->setPermission("command.setlevel");
                parent::__construct($plugin, "setlevel", "Allows you to set a player's level", "/setlevel <player> <level>", []);
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

                $level = intval($args[1]);
                if(!is_numeric($level) or !is_int($level)){
                        $sender->sendMessage(RandomUtils::colorMessage("The level must be an integer."));
                        return false;
                }

                $maxLevel = count($this->core->module_configurations->levels) - 1;
                if($level < 1 or $level > $maxLevel){
                        $sender->sendMessage(RandomUtils::colorMessage("The level must be between 1 and " . $maxLevel . "."));
                        return false;
                }

                $player = $args[0];
                $this->core->module_loader->levels->setLevel($player, $level);
                $sender->sendMessage(RandomUtils::colorMessage(str_replace(["@player", "@level"], [$player, $args[1]], $this->core->getMessage("levels", "player_level_set"))));

                return true;
        }
}