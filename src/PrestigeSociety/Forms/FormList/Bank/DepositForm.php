<?php
namespace PrestigeSociety\Forms\FormList\Bank;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\UIForms\CustomForm;
class DepositForm extends FormHandler{
        public function send(Player $player){
                $form = new CustomForm($this);

                $form->setTitle(RandomUtils::colorMessage("&8&lDEPOSIT MONEY"));
                $money = $this->core->module_loader->economy->getMoney($player);
                $cash = $this->core->module_loader->economy->getCash($player);

                $form->setLabel(RandomUtils::colorMessage("&7Your balance: &f$$money"));
                $form->setLabel(RandomUtils::colorMessage("&7Cash balance: &f$$cash"));
                $form->setInput(RandomUtils::colorMessage("&7amount (enter 0 to deposit all)"));

                $form->send($player);
        }

        public function handleResponse(Player $player, $formData){
                $economy = $this->core->module_loader->economy;

                if(!is_numeric($formData[2])  || ((int) $formData[2] < 0)){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                            RandomUtils::colorMessage("{$formData[1]} is not a valid number."), $this->form_id, []
                        ]);
                        return;
                }

                $amount = (int) $formData[2];
                if($amount === 0){
                        $amount = $economy->getCash($player);
                }

                if(!$economy->deposit($player, $amount)){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                            RandomUtils::colorMessage("You do not have enough to deposit.\nYou have: $" . $economy->getCash($player) . "\nYou need: $$amount"), $this->form_id, []
                        ]);
                        return;
                }

                $message = $this->core->getMessage("economy", "deposit_success");
                $message = str_replace("@money", $amount, $message);
                $player->sendMessage(RandomUtils::colorMessage($message));
        }
}