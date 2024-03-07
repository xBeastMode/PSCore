<?php
namespace PrestigeSociety\Teleport\Commands\Spawn;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class SpawnCommand extends CoreCommand{
        /**
         * SpawnCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                parent::__construct($plugin, "spawn", "Teleport to spawn.", RandomUtils::colorMessage("&eUsage: /spawn"), []);
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

                $delay = $this->core->module_loader->teleport->getTeleportDelay($sender);
                $message = $this->core->module_loader->teleport->getTeleportMessage($sender, ["vars" => ["@seconds" => $delay]]);

                $this->core->module_loader->teleport->teleport($sender, $this->core->module_loader->teleport->getSpawn(), $delay);
                $sender->sendMessage(RandomUtils::colorMessage($message));

                return true;
        }
}