<?php
namespace PrestigeSociety\Core\Commands;
use PrestigeSociety\Core\PrestigeSocietyCore;
use pocketmine\command\CommandSender;
use PrestigeSociety\Forms\FormList\Blacksmith\BlacksmithForm;
use PrestigeSociety\Forms\FormList\Blacksmith\ChooseDamageRepairForm;
use PrestigeSociety\Forms\FormList\Blacksmith\ConfirmRepairForm;
use PrestigeSociety\Forms\FormList\Blacksmith\RenameForm;
use PrestigeSociety\Forms\FormManager;
class BlacksmithCommand extends CoreCommand{
        protected int $RENAME_ID = 0;
        protected int $CONFIRM_REPAIR_ID;
        protected int $CHOOSE_DAMAGE_REPAIR = 0;
        protected int $BLACKSMITH_ID = 0;

        /**
         * BlacksmithCommand constructor.
         * 
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                $this->BLACKSMITH_ID = FormManager::getNextFormId();
                $this->CHOOSE_DAMAGE_REPAIR = FormManager::getNextFormId();
                $this->CONFIRM_REPAIR_ID = FormManager::getNextFormId();
                $this->RENAME_ID = FormManager::getNextFormId();

                $plugin->module_loader->form_manager->registerHandler($this->BLACKSMITH_ID, BlacksmithForm::class);
                $plugin->module_loader->form_manager->registerHandler($this->CHOOSE_DAMAGE_REPAIR, ChooseDamageRepairForm::class);
                $plugin->module_loader->form_manager->registerHandler($this->CONFIRM_REPAIR_ID, ConfirmRepairForm::class);
                $plugin->module_loader->form_manager->registerHandler($this->RENAME_ID, RenameForm::class);

                parent::__construct($plugin, "blacksmith", "rename and repair the item in your hand", "", []);
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

                $options = ["check_permissions" => true, "message" => $this->core->getMessage("command_lock", "blacksmith")];
                $this->core->module_loader->form_manager->sendForm($this->BLACKSMITH_ID, $sender, [$this->RENAME_ID, $this->CHOOSE_DAMAGE_REPAIR, $this->CONFIRM_REPAIR_ID], $options);
                return true;
        }
}