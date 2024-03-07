<?php
namespace PrestigeSociety\Teleport\Commands\Warp;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class InstantWarpCommand extends CoreCommand{
        /**
         * InstantWarpCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                $this->setPermission("command.instantwarp");
                parent::__construct($plugin, "instantwarp", "Teleport player to a warp instantly", RandomUtils::colorMessage("&eUsage: /instantwarp [player] [warp] [owner]"), ["instawarp"]);
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

                if(count($args) < 2){
                        $warps = $this->core->module_loader->teleport->warp_api->getWarps();
                        $warp_names = implode(", ", array_map(function (array $data){ return $data["name"]; }, $warps));

                        $message = $this->core->module_loader->teleport->getMessage("warp_list");
                        $message = str_replace("@warps", $warp_names, $message);

                        $sender->sendMessage(RandomUtils::colorMessage($message));
                        return false;
                }

                /** @var Player $sender */

                $player = $args[0];
                $warp = $args[1];

                if(($player = $sender->getServer()->getPlayerByPrefix($player)) === null){
                        $sender->sendMessage(RandomUtils::colorMessage(str_replace("@player", $args[0], $this->core->module_loader->teleport->getMessage("offline"))));
                        return false;
                }

                $position = $this->core->module_loader->teleport->warp_api->getRelativeWarpPosition($player, $warp, $args[2] ?? null);

                if($position === null){
                        $message = $this->core->module_loader->teleport->getMessage("unknown_warp");
                        $message = str_replace("@warp", $warp, $message);

                        $sender->sendMessage(RandomUtils::colorMessage($message));
                        return false;
                }

                $player->teleport($position);

                $message = $this->core->module_loader->teleport->getMessage("instant_warp");
                $message = str_replace(["@player", "@warp"], [$player->getName(), $warp], $message);

                $sender->sendMessage(RandomUtils::colorMessage($message));

                return true;
        }
}