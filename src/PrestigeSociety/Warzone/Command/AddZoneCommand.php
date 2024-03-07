<?php
namespace PrestigeSociety\Warzone\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class AddZoneCommand extends CoreCommand{
        /**
         * AddZoneCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                $this->setPermission("command.addzone");
                parent::__construct($plugin, "addzone", "Adds warzone loot crate zone", RandomUtils::colorMessage("&eUsage: /addzone <name>"), []);
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
                if(count($args) <= 0){
                        $sender->sendMessage($this->getUsage());
                        return;
                }

                /** @var Player $sender */

                $zone = $args[0];
                $this->core->module_loader->warzone->addZone($zone, $sender->getLocation());

                $message = $this->core->getMessage("warzone", "added_zone");
                $message = str_replace("@zone", $zone, $message);

                $sender->sendMessage(RandomUtils::colorMessage($message));
        }
}