<?php
namespace PrestigeSociety\Player\Commands;
use Exception;
use pocketmine\player\Player;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use pocketmine\command\CommandSender;
class ProfileCommand extends CoreCommand {
        /**
         * ProfileCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                parent::__construct($plugin, "profile", "look at a player's profile", "Usage: /profile [player]", []);
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
                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->player_data->PROFILE_ID, $sender, $args[0] ?? $sender->getName());
                return true;
        }
}