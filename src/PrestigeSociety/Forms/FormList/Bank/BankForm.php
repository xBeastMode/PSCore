<?php
namespace PrestigeSociety\Forms\FormList\Bank;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\UIForms\SimpleForm;
class BankForm extends FormHandler{
        public function send(Player $player){
                $form = new SimpleForm($this);

                $form->setTitle(RandomUtils::colorMessage("&l&8BANK ACCOUNT"));
                $money = $this->core->module_loader->economy->getMoney($player);
                $form->setContent(RandomUtils::colorMessage("&7Your balance: &f$$money"));

                $form->setButton(RandomUtils::colorMessage("&8- &8deposit &8-"));
                $form->setButton(RandomUtils::colorMessage("&8- &8pay player &8-"));
                $form->setButton(RandomUtils::colorMessage("&8- &8withdraw &8-"));

                $form->send($player);
        }

        public function handleResponse(Player $player, $formData){
                $economy = $this->core->module_loader->economy;
                $formIds = [$economy->DEPOSIT_ID, $economy->PAY_ID, $economy->WITHDRAW_ID];

                $this->core->module_loader->form_manager->sendForm($formIds[$formData], $player);
        }
}