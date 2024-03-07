<?php
namespace PrestigeSociety\Teleport\Commands\Home;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Player\Data\Settings;
use PrestigeSociety\Teleport\Teleport;

class SetHomeCommand extends CoreCommand{
        /**
         * SetHomeCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                parent::__construct($plugin, "sethome", "Set a home where you are standing.", RandomUtils::colorMessage("&eUsage: /sethome <name>"), []);
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
                        $sender->sendMessage($this->getUsage());
                        return false;
                }

                /** @var Player $sender */

                $homes = $this->core->module_loader->teleport->home_api->getPlayerHomes($sender);
                $settings = $this->core->module_loader->player_data->getPlayerSettings($sender);
                $max_homes = $settings->get(Settings::SETTING_MAX_HOMES, Teleport::DEFAULT_MAX_HOMES);

                if(count($homes) >= $max_homes){
                        $message = $this->core->module_loader->teleport->getMessage("max_homes_reached");
                        $message = str_replace(["@homes", "@max_homes"], [count($homes), $max_homes], $message);

                        $sender->sendMessage(RandomUtils::colorMessage($message));
                        return false;
                }

                $home = $args[0];
                $location = $sender->getLocation();
                $this->core->module_loader->teleport->home_api->setHome($sender, $home, $location->x, $location->y, $location->z, $location->world->getDisplayName());

                $message = $this->core->module_loader->teleport->getMessage("set_home");
                $message = str_replace(["@home", "@x", "@y", "@z", "@level"], [$home, $location->x, $location->y, $location->z, $location->world->getDisplayName()], $message);

                $sender->sendMessage(RandomUtils::colorMessage($message));
                return true;
        }
}