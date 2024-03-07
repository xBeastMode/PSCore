<?php
namespace PrestigeSociety\Levels\Commands;
use pocketmine\player\Player;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use pocketmine\command\CommandSender;
use PrestigeSociety\Core\Utils\RandomUtils;
class PrestigeCommand extends CoreCommand{
        /**
         * PrestigeCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                parent::__construct($plugin, "prestige", "Prestige if you reached max rank!", "Usage: /prestige", []);
        }

        /**
         * @param CommandSender $sender
         * @param string        $commandLabel
         * @param string[]      $args
         *
         * @return bool
         */
        public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
                if(!$this->testPlayer($sender)){
                        return false;
                }

                /** @var Player $sender */

                if($this->core->module_loader->economy->getCash($sender) > 0){
                        $message = $this->core->getMessage("levels", "deposit_cash");
                        $sender->sendMessage(RandomUtils::colorMessage($message));
                        return false;
                }

                if($this->core->module_loader->levels->levelUp($sender)){
                        $level = $this->core->module_loader->levels->getLevel($sender);
                        $this->core->getServer()->broadcastMessage(RandomUtils::colorMessage(str_replace(["@player", "@level"], [$sender->getName(), $level],
                            $this->core->getMessage("levels", "level_up"))));
                }else{
                        $rank = "Z";
                        $levels_config = $this->core->module_configurations->levels;
                        $level = $this->core->module_loader->levels->getLevel($sender) + 1;

                        if(isset($levels_config[$level])){
                                $level_config = $levels_config[$level];
                                $rank = $level_config["required_rank"];
                        }

                        $message = $this->core->getMessage("levels", "level_up_failed");
                        $message = str_replace("@rank", $rank, $message);
                        $sender->sendMessage(RandomUtils::colorMessage($message));
                }
                return true;
        }
}