<?php
namespace PrestigeSociety\ProtectionStones\Commands;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
class ProtectionStonesCommand extends CoreCommand{
        /**
         * ProtectionStonesCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                parent::__construct($plugin, "pstones", "Manage your protection stones", "Usage: /pstones", []);
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

                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MANAGE_STONES_ID, $sender);
                return true;
        }
}