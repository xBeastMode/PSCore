<?php
namespace PrestigeSociety\Spawners;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Forms\FormList\Spawners\ConfirmPurchaseForm;
use PrestigeSociety\Forms\FormList\Spawners\SelectSpawnerForm;
use PrestigeSociety\Forms\FormManager;
class Spawners{
        /** @var int */
        public int $CONFIRM_PURCHASE_ID = 0;
        public int $SELECT_SPAWNER_ID = 0;

        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /**
         * Spawners constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->SELECT_SPAWNER_ID = FormManager::getNextFormId();
                $this->CONFIRM_PURCHASE_ID = FormManager::getNextFormId();

                $this->core = $core;

                $this->core->module_loader->form_manager->registerHandler($this->SELECT_SPAWNER_ID, SelectSpawnerForm::class);
                $this->core->module_loader->form_manager->registerHandler($this->CONFIRM_PURCHASE_ID, ConfirmPurchaseForm::class);

                $this->core->getServer()->getPluginManager()->registerEvents(new SpawnersListener($core), $core);
        }
}