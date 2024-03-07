<?php
namespace PrestigeSociety\Teleport\Commands\Warp;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Teleport\Teleport;
class WarpCommand extends CoreCommand{
        /**
         * WarpCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                parent::__construct($plugin, "warp", "Teleport to warps.", RandomUtils::colorMessage("&eUsage: /warp [name] [owner]"), []);
        }

        /**
         * @param CommandSender $sender
         * @param string        $commandLabel
         * @param string[]      $args
         *
         * @return bool
         */
        public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
                if(!$this->testPlayer($sender)){
                        return false;
                }

                if(count($args) <= 0){
                        $warps = $this->core->module_loader->teleport->warp_api->getWarps();
                        $warp_names = implode(", ", array_map(function (array $data){ return $data["name"]; }, $warps));

                        $message = $this->core->module_loader->teleport->getMessage("warp_list");
                        $message = str_replace("@warps", $warp_names, $message);

                        $sender->sendMessage(RandomUtils::colorMessage($message));
                        return false;
                }

                /** @var Player $sender */

                $warp = $args[0];
                if(!$sender->hasPermission("warp.$warp")){
                        $message = $this->core->module_loader->teleport->getMessage("no_permission");
                        $message = str_replace("@warp", $warp, $message);

                        $sender->sendMessage(RandomUtils::colorMessage($message));
                        return false;
                }

                $position = $this->core->module_loader->teleport->warp_api->getRelativeWarpPosition($sender, $warp, $args[1] ?? null);
                if($position === null){
                        $message = $this->core->module_loader->teleport->getMessage("unknown_warp");
                        $message = str_replace("@warp", $warp, $message);

                        $sender->sendMessage(RandomUtils::colorMessage($message));
                        return false;
                }

                $delay = $this->core->module_loader->teleport->warp_api->getTeleportDelay($sender);
                $message = $this->core->module_loader->teleport->getTeleportMessage($sender, ["module" => "warp", "vars" => ["@warp" => $warp, "@seconds" => $delay], "permission" => Teleport::INSTANT_WARP_TELEPORT_PERMISSION]);

                $this->core->module_loader->teleport->teleport($sender, $position, $delay);
                $sender->sendMessage(RandomUtils::colorMessage($message));

                return true;
        }
}