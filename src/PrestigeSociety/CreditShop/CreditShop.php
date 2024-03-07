<?php
namespace PrestigeSociety\CreditShop;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Forms\FormList\CreditShop\ChooseItemForm;
use PrestigeSociety\Forms\FormList\CreditShop\ConfirmPurchaseForm;
use PrestigeSociety\Forms\FormManager;
class CreditShop{
        /** @var int */
        public int $CONFIRM_PURCHASE_ID = 0;
        public int $CHOOSE_ITEM_ID = 0;

        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /**
         * CreditShop constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;

                $this->CHOOSE_ITEM_ID = FormManager::getNextFormId();
                $this->CONFIRM_PURCHASE_ID = FormManager::getNextFormId();

                $this->core->module_loader->form_manager->registerHandler($this->CHOOSE_ITEM_ID, ChooseItemForm::class);
                $this->core->module_loader->form_manager->registerHandler($this->CONFIRM_PURCHASE_ID, ConfirmPurchaseForm::class);
        }
}