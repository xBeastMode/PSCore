<?php
namespace PrestigeSociety\Warzone\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class DespawnLootCrateCommand extends CoreCommand{
        /**
         * DespawnLootCrateCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                $this->setPermission("command.despawncrate");
                parent::__construct($plugin, "despawncrate", "Despawns warzone loot crate", RandomUtils::colorMessage("&eUsage: /despawncrate"), ["dlc"]);
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

                $this->core->module_loader->warzone->despawnLootCrate();

                $message = $this->core->getMessage("warzone", "despawned_crate");
                $sender->sendMessage(RandomUtils::colorMessage($message));
        }
}