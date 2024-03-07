<?php
namespace PrestigeSociety\Teleport\Commands\Home;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
class DeleteHomeCommand extends CoreCommand{
        /**
         * DeleteHomeCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                parent::__construct($plugin, "deletehome", "Delete a home.", RandomUtils::colorMessage("&eUsage: /deletehome <name>"), []);
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

                $home = $args[0];

                if(!$this->core->module_loader->teleport->home_api->homeExists($sender, $home)){
                        $message = $this->core->module_loader->teleport->getMessage("unknown_home");
                        $message = str_replace("@home", $home, $message);

                        $sender->sendMessage(RandomUtils::colorMessage($message));
                        return false;
                }

                $this->core->module_loader->teleport->home_api->deleteHome($sender, $home);

                $message = $this->core->module_loader->teleport->getMessage("delete_home");
                $message = str_replace("@home", $home, $message);

                $sender->sendMessage(RandomUtils::colorMessage($message));
                return true;
        }
}