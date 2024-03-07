<?php
namespace PrestigeSociety\Levels\Commands;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use pocketmine\command\CommandSender;
class StatsCommand extends CoreCommand{
        /**
         * StatsCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                parent::__construct($plugin, "stats", "Allows you to see a player's statistics", "/stats [player]", []);
        }

        /**
         * @param CommandSender $sender
         * @param string        $commandLabel
         * @param string[]      $args
         *
         * @return bool
         */
        public function execute(CommandSender $sender,string $commandLabel, array $args): bool{
                if(count($args) < 1){
                        if(!$this->testPlayer($sender)){
                                return false;
                        }
                        
                        $level = $this->core->module_loader->levels->getLevel($sender);
                        $kills = $this->core->module_loader->levels->getKills($sender);
                        $deaths = $this->core->module_loader->levels->getDeaths($sender);
                        $sender->sendMessage(RandomUtils::colorMessage(str_replace(["@player", "@level", "@kills", "@deaths"], [$sender->getName(), $level, $kills, $deaths], $this->core->getMessage("levels", "player_stats"))));

                        return false;
                }

                $player = $args[0];

                $kills = $this->core->module_loader->levels->getKills($player);
                $deaths = $this->core->module_loader->levels->getDeaths($player);
                $level = $this->core->module_loader->levels->getLevel($player);

                $sender->sendMessage(RandomUtils::colorMessage(str_replace(["@player", "@level", "@kills", "@deaths"], [$player, $level, $kills, $deaths], $this->core->getMessage("levels", "player_stats_other"))));
                
                return true;
        }
}