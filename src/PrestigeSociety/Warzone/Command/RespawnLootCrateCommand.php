<?php
namespace PrestigeSociety\Warzone\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class RespawnLootCrateCommand extends CoreCommand{
        /**
         * RespawnLootCrateCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                $this->setPermission("command.respawncrate");
                parent::__construct($plugin, "respawncrate", "Respawns warzone loot crate", RandomUtils::colorMessage("&eUsage: /respawncrate [zone]"), ["rlc"]);
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

                $zone = $args[0] ?? null;
                $this->core->module_loader->warzone->respawnLootCrate($zone);

                if($zone !== null){
                        $message = $this->core->getMessage("warzone", "respawned_crate_zone");
                        $message = str_replace("@zone", $zone, $message);
                }else{
                        $message = $this->core->getMessage("warzone", "respawned_crate");
                }
                $sender->sendMessage(RandomUtils::colorMessage($message));
        }
}