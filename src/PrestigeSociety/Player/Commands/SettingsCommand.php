<?php
namespace PrestigeSociety\Player\Commands;
use Exception;
use pocketmine\player\Player;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use pocketmine\command\CommandSender;
class SettingsCommand extends CoreCommand {
        /**
         * SettingsCommand constructor.
         * 
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                parent::__construct($plugin, "settings", "change your player settings", "Usage: /settings", []);
        }

        /**
         * @param CommandSender $sender
         * @param string        $commandLabel
         * @param string[]      $args
         *
         * @return bool
         *
         * @throws Exception
         */
        public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
                if(!$this->testPlayer($sender)){
                        return false;
                }

                /** @var Player $sender */
                $options = ["check_permissions" => true, "message" => $this->core->getMessage("command_lock", "settings")];
                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->player_data->PLAYER_SETTINGS_ID, $sender, [], $options);
                return true;
        }
}