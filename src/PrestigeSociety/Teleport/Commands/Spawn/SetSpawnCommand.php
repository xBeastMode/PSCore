<?php
namespace PrestigeSociety\Teleport\Commands\Spawn;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class SetSpawnCommand extends CoreCommand{
        /**
         * SetSpawnCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                $this->setPermission("command.setspawn");
                parent::__construct($plugin, "setspawn", "Set spawn where you are standing.", RandomUtils::colorMessage("&eUsage: /setspawn"), []);
        }

        /**
         * @param CommandSender $sender
         * @param string        $commandLabel
         * @param string[]      $args
         *
         * @return bool
         */
        public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
                if(!$this->testAll($sender)){
                        return false;
                }
                /** @var Player $sender */

                $this->core->module_loader->teleport->setSpawn($sender->getPosition());

                $message = $this->core->module_loader->teleport->getMessage("set_spawn");
                $message = str_replace(["@x", "@y", "@z", "@level"], [$sender->getLocation()->x, $sender->getLocation()->y, $sender->getLocation()->z, $sender->getWorld()->getDisplayName()], $message);

                $sender->sendMessage(RandomUtils::colorMessage($message));
                return true;
        }
}