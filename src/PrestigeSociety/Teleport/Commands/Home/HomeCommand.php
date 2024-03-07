<?php
namespace PrestigeSociety\Teleport\Commands\Home;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Teleport\Teleport;
class HomeCommand extends CoreCommand{
        /**
         * HomeCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                parent::__construct($plugin, "home", "Teleport to your homes.", RandomUtils::colorMessage("&eUsage: /home [name]"), []);
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
                        $homes = $this->core->module_loader->teleport->home_api->getPlayerHomes($sender);
                        $home_names = implode(", ", array_map(function (array $data){ return $data["name"]; }, $homes));

                        $message = $this->core->module_loader->teleport->getMessage("home_list");
                        $message = str_replace("@homes", $home_names, $message);

                        $sender->sendMessage(RandomUtils::colorMessage($message));
                        return false;
                }

                /** @var Player $sender */

                $home = $args[0];
                $position = $this->core->module_loader->teleport->home_api->getHomePosition($sender, $home);

                if($position === null){
                        $message = $this->core->module_loader->teleport->getMessage("unknown_home");
                        $message = str_replace("@home", $home, $message);

                        $sender->sendMessage(RandomUtils::colorMessage($message));
                        return false;
                }

                $delay = $this->core->module_loader->teleport->home_api->getTeleportDelay($sender);
                $message = $this->core->module_loader->teleport->getTeleportMessage($sender, ["module" => "home", "vars" => ["@home" => $home, "@seconds" => $delay], "permission" => Teleport::INSTANT_HOME_TELEPORT_PERMISSION]);

                $this->core->module_loader->teleport->teleport($sender, $position, $delay);
                $sender->sendMessage(RandomUtils::colorMessage($message));

                return true;
        }
}