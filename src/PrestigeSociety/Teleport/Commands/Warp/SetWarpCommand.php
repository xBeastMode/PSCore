<?php
namespace PrestigeSociety\Teleport\Commands\Warp;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Teleport\Teleport;
class SetWarpCommand extends CoreCommand{
        /**
         * SetWarpCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                $this->setPermission("command.setwarp");
                parent::__construct($plugin, "setwarp", "Set a warp where you are standing.", RandomUtils::colorMessage("&eUsage: /setwarp <name> [owner]"), []);
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

                if(count($args) <= 0){
                        $sender->sendMessage($this->getUsage());
                        return false;
                }

                /** @var Player $sender */

                $warp = $args[0];
                $owner = $args[1] ?? $sender->getName();

                $warp_info = $this->core->module_loader->teleport->warp_api->getWarpInfo($warp, $owner);
                if(isset($warp_info[0]) && strtoupper($owner) === Teleport::DEFAULT_OWNER){
                        $message = $this->core->module_loader->teleport->getMessage("no_access");

                        $sender->sendMessage(RandomUtils::colorMessage($message));
                        return false;
                }

                $this->core->module_loader->teleport->warp_api->setWarp($warp, $sender->getLocation()->x, $sender->getLocation()->y, $sender->getLocation()->z, $sender->getWorld()->getDisplayName(), $owner);

                $message = $this->core->module_loader->teleport->getMessage("set_warp");
                $message = str_replace(["@warp", "@x", "@y", "@z", "@level"], [$warp, $sender->getLocation()->x, $sender->getLocation()->y, $sender->getLocation()->z, $sender->getWorld()->getDisplayName()], $message);

                $sender->sendMessage(RandomUtils::colorMessage($message));
                return true;
        }
}