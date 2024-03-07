<?php
namespace PrestigeSociety\Recovery\Commands;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class RecoveryCommand extends CoreCommand{
        /**
         * RecoveryCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                $this->setPermission("command.recovery");
                parent::__construct($plugin, "recovery", "opens recovery inventory", "Usage: /recovery [player]", []);
        }

        /**
         * @param CommandSender $sender
         * @param string        $commandLabel
         * @param array         $args
         *
         * @return void
         */
        public function execute(CommandSender $sender, string $commandLabel, array $args){
                if(!$this->testPermission($sender) || !$this->testPlayer($sender)){
                        return;
                }

                /** @var Player $sender */

                $username = $args[0] ?? $sender->getName();
                if(!$this->core->module_loader->recovery->openRecoveryInventory($sender, $username)){
                        $message = $this->core->getMessage("recovery", "no_player");
                        $sender->sendMessage(RandomUtils::colorMessage($message));
                }
        }
}