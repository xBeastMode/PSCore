<?php
namespace PrestigeSociety\Core\Commands;
use pocketmine\player\Player;
use PrestigeSociety\Core\PrestigeSocietyCore;
use pocketmine\command\CommandSender;
class HUDCommand extends CoreCommand{
        /**
         * HUDCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                parent::__construct($plugin, "hud", "Toggle HUD", "Usage: /hud", []);
        }

        /**
         * @param CommandSender $sender
         * @param string        $commandLabel
         * @param string[]      $args
         *
         * @return bool
         */
        public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
                if($this->testPlayer($sender)){
                        return false;
                }

                /** @var Player $sender */
                $this->core->module_loader->hud->toggleHUD($sender);
                return true;
        }
}