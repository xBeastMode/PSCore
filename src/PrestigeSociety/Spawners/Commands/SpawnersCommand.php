<?php
namespace PrestigeSociety\Spawners\Commands;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
class SpawnersCommand extends CoreCommand{
        /**
         * SpawnersCommand constructor.
         * 
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                parent::__construct($plugin, 'spawners', 'ShopModel for spawners', 'Usage: /spawners', []);
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

                /** @var Player $sender */

                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->spawners->SELECT_SPAWNER_ID, $sender);
                return true;
        }
}