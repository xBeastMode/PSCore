<?php
namespace PrestigeSociety\Forms\FormList\Bank;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\UIForms\CustomForm;
class PayForm extends FormHandler{
        public function send(Player $player){
                $form = new CustomForm($this);

                $form->setTitle(RandomUtils::colorMessage("&l&8PAY PLAYER"));
                $money = $this->core->module_loader->economy->getMoney($player);

                $form->setLabel(RandomUtils::colorMessage("&7Your balance: &f$$money"));
                $form->setInput(RandomUtils::colorMessage("&7receiver"));
                $form->setInput(RandomUtils::colorMessage("&7amount"));

                $form->send($player);
        }

        public function handleResponse(Player $player, $formData){
                $economy = $this->core->module_loader->economy;

                if(!$economy->playerExists($formData[1])){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                            RandomUtils::colorMessage("{$formData[1]} is not registered in our economy."), $this->form_id, []
                        ]);
                        return;
                }
                if(!is_numeric($formData[2]) || ((int) $formData[2] < 0)){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                            RandomUtils::colorMessage("{$formData[2]} is not a valid number."), $this->form_id, []
                        ]);
                        return;
                }

                $amount = (int) $formData[2];
                if(($money = $economy->getMoney($player)) < $amount){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                            RandomUtils::colorMessage("You have insufficient funds.\nYou have: $$money\nNeeded: $$amount"), $this->form_id, []
                        ]);
                        return;
                }

                $economy->payMoney($player, $formData[1], $amount);
                $message = $this->core->getMessage("economy", "payed_money");
                $message = str_replace(["@money", "@player"], [$amount, $formData[1]], $message);
                $player->sendMessage(RandomUtils::colorMessage($message));

                if(($playerR = $player->getServer()->getPlayerByPrefix($formData[1])) !== null){
                        $message = $this->core->getMessage("economy", "payed_money_notification");
                        $message = str_replace(["@player", "@money"], [$player->getName(), $amount], $message);
                        $playerR->sendMessage(RandomUtils::colorMessage($message));
                }
        }
}