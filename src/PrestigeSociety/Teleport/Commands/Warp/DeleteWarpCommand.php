<?php
namespace PrestigeSociety\Teleport\Commands\Warp;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Teleport\Teleport;
class DeleteWarpCommand extends CoreCommand{
        /**
         * DeleteWarpCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                $this->setPermission("command.deletewarp");
                parent::__construct($plugin, "deletewarp", "Delete a warp.", RandomUtils::colorMessage("&eUsage: /deletewarp <name> [owner]"), []);
        }

        /**
         * @param CommandSender $sender
         * @param string        $commandLabel
         * @param string[]      $args
         *
         * @return bool
         */
        public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
                if(!$this->testPermission($sender)){
                        return false;
                }

                if(count($args) <= 0){
                        $sender->sendMessage($this->getUsage());
                        return false;
                }

                /** @var Player $sender */

                $warp = $args[0];
                $owner = $args[1] ?? null;

                if(!$this->core->module_loader->teleport->warp_api->warpExists($warp, $owner)){
                        $message = $this->core->module_loader->teleport->getMessage("unknown_warp");
                        $message = str_replace("@warp", $warp, $message);

                        $sender->sendMessage(RandomUtils::colorMessage($message));
                        return false;
                }

                $warp_info = $this->core->module_loader->teleport->warp_api->getWarpInfo($warp, $owner);
                if($warp_info[0]["owner"] === Teleport::DEFAULT_OWNER){
                        $message = $this->core->module_loader->teleport->getMessage("no_access");

                        $sender->sendMessage(RandomUtils::colorMessage($message));
                        return false;
                }

                $this->core->module_loader->teleport->warp_api->deleteWarp($warp, $owner);

                $message = $this->core->module_loader->teleport->getMessage("delete_warp");
                $message = str_replace("@warp", $warp, $message);

                $sender->sendMessage(RandomUtils::colorMessage($message));
                return true;
        }
}