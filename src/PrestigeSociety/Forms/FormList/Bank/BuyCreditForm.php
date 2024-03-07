<?php
namespace PrestigeSociety\Forms\FormList\Bank;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\Forms\FormList\Stones\MessageForm;
use PrestigeSociety\UIForms\SimpleForm;
class BuyCreditForm extends FormHandler{
        public function send(Player $player){
                $cost = (int) $this->core->getConfig()->get("credit_cost");

                $form = new SimpleForm($this);
                $form->setTitle(RandomUtils::colorMessage("&c&lBUY CREDIT"));

                $content = "===========================\n";
                $content .= "&7credit cost: &f$cost\n";
                $content .= "===========================\n";
                $form->setContent(RandomUtils::colorMessage($content));
                $form->setButton(RandomUtils::colorMessage("&8buy"));
                $form->setButton(RandomUtils::colorMessage("&8cancel"));
                $form->send($player);
        }

        public function handleResponse(Player $player, $formData){
                $cost = (int) $this->core->getConfig()->get("credit_cost");

                if($formData === 0){
                        if(($money = $this->core->module_loader->economy->getMoney($player)) < $cost){
                                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                                    RandomUtils::colorMessage("You don't have enough cash to purchase this\nYou need: {$cost}\nYou have: $money."), $this->form_id, MessageForm::METADATA_CLOSE
                                ]);
                                return;
                        }

                        $this->core->module_loader->economy->subtractMoney($player, $cost);
                        $this->core->module_loader->credits->addCredits($player, 1);

                        $message = $this->core->getMessage("economy", "purchase_credit");
                        $player->sendMessage(RandomUtils::colorMessage($message));
                }else{
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->economy->BANK_ID, $player);
                }
        }
}