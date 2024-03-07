<?php
namespace PrestigeSociety\Forms\FormList\Bank;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\Forms\FormList\Stones\MessageForm;
use PrestigeSociety\UIForms\CustomForm;
class WithdrawForm extends FormHandler{
        public function send(Player $player){
                $form = new CustomForm($this);

                $form->setTitle(RandomUtils::colorMessage("&l&8WITHDRAW MONEY"));
                $money = $this->core->module_loader->economy->getMoney($player);

                $form->setLabel(RandomUtils::colorMessage("&7Your balance: &f$$money"));
                $form->setInput(RandomUtils::colorMessage("&7amount (enter 0 to withdraw all)"));

                $form->send($player);
        }

        public function handleResponse(Player $player, $formData){
                $economy = $this->core->module_loader->economy;

                if(!is_numeric($formData[1])  || ((int) $formData[1] < 0)){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                            RandomUtils::colorMessage("{$formData[1]} is not a valid number."), $this->form_id, []
                        ]);
                        return;
                }

                $amount = (int) $formData[1];
                if(($money = $economy->getMoney($player)) < $amount){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                            RandomUtils::colorMessage("You have insufficient funds.\nYou have: $$money\nNeeded: $$amount"), $this->form_id, []
                        ]);
                        return;
                }

                if($amount === 0){
                        if($money <= 0){
                                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                                    RandomUtils::colorMessage("You have no funds in your account."), $this->form_id, MessageForm::METADATA_CLOSE
                                ]);
                                return;
                        }
                        $amount = $money;
                }


                if(!$economy->withdraw($player, $amount, null, true)){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                            RandomUtils::colorMessage("Please make space in your inventory to add the cash."), $this->form_id, MessageForm::METADATA_CLOSE
                        ]);
                        return;
                }

                $message = $this->core->getMessage("economy", "withdraw_success");
                $message = str_replace("@money", $amount, $message);
                $player->sendMessage(RandomUtils::colorMessage($message));
        }
}