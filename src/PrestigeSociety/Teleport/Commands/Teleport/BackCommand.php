<?php
namespace PrestigeSociety\Teleport\Commands\Teleport;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Teleport\EventListener;
use PrestigeSociety\Teleport\Teleport;
class BackCommand extends CoreCommand{
        /**
         * BackCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                $this->setPermission("command.back");
                parent::__construct($plugin, "back", "Go back to your last position.", RandomUtils::colorMessage("&eUsage: /back"), []);
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

                if(!isset(EventListener::$last_position[spl_object_hash($sender)])){
                        $sender->sendMessage(RandomUtils::colorMessage($this->core->module_loader->teleport->getMessage("no_back")));
                        return false;
                }

                $position = EventListener::$last_position[spl_object_hash($sender)];

                $delay = $this->core->module_loader->teleport->getTeleportDelay($sender, ["module" => "back", "permission" => Teleport::INSTANT_BACK_TELEPORT_PERMISSION]);
                $message = $this->core->module_loader->teleport->getTeleportMessage($sender, ["module" => "back", "vars" => ["@seconds" => $delay], "permission" => Teleport::INSTANT_BACK_TELEPORT_PERMISSION]);

                $this->core->module_loader->teleport->teleport($sender, $position, $delay);
                $sender->sendMessage(RandomUtils::colorMessage($message));

                return true;
        }
}