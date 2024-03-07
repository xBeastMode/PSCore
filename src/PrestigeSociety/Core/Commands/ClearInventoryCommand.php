<?php
namespace PrestigeSociety\Core\Commands;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormList\Player\ClearInventoryForm;
use PrestigeSociety\Forms\FormManager;
class ClearInventoryCommand extends CoreCommand{
        protected int $CLEAR_INVENTORY_COMMAND = 0;

        /**
         * ClearInventoryCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                parent::__construct($plugin, "clearinventory", "Clear your inventory instantly!", RandomUtils::colorMessage("&e/ci"), ["ci"]);
                $this->setPermission("command.ci");

                $this->CLEAR_INVENTORY_COMMAND = FormManager::getNextFormId();
                $plugin->module_loader->form_manager->registerHandler($this->CLEAR_INVENTORY_COMMAND, ClearInventoryForm::class);
        }

        /**
         * @param CommandSender $sender
         * @param string        $commandLabel
         * @param string[]      $args
         *
         * @return bool
         *
         */
        public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
                if(!$this->testAll($sender)){
                        return false;
                }

                /** @var Player $sender */
                $this->core->module_loader->form_manager->sendForm($this->CLEAR_INVENTORY_COMMAND, $sender);
                return true;
        }
}