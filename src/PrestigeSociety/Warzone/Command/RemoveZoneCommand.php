<?php
namespace PrestigeSociety\Warzone\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class RemoveZoneCommand extends CoreCommand{
        /**
         * RemoveZoneCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                $this->setPermission("command.removezone");
                parent::__construct($plugin, "removezone", "Removes warzone loot crate zone", RandomUtils::colorMessage("&eUsage: /removezone <name>"), ["rmzone"]);
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
                $this->core->module_loader->warzone->removeZone($zone);

                $message = $this->core->getMessage("warzone", "removed_zone");
                $message = str_replace("@zone", $zone, $message);

                $sender->sendMessage(RandomUtils::colorMessage($message));
        }
}