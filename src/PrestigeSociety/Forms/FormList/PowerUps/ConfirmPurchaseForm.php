<?php
namespace PrestigeSociety\Forms\FormList\PowerUps;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\Forms\FormList\Stones\MessageForm;
use PrestigeSociety\UIForms\CustomForm;
class ConfirmPurchaseForm extends FormHandler{
        public function send(Player $player){
                $powerUp = $this->getData();
                $cost = (int) $this->core->getConfig()->get('power_ups')[str_replace(" ", "_", strtolower($powerUp[1]))];

                $form = new CustomForm($this);
                $form->setTitle(RandomUtils::colorMessage("&8&lPOWER-UPS"));
                $content = "===========================\n";
                $content .= "&7You are purchasing: &f$powerUp[1]\n\n";
                $content .= "&7Cost per hour: &f$cost&f\n";
                $content .= "===========================\n";
                $form->setLabel(RandomUtils::colorMessage($content));
                $form->setSlider(RandomUtils::colorMessage("&7hours"), 1, 24, 1, 1);
                $form->send($player);
        }

        public function handleResponse(Player $player, $formData){
                $powerUp = $this->getData();
                $cost = (int) $this->core->getConfig()->get('power_ups')[str_replace(" ", "_", strtolower($powerUp[1]))];
                $cost = $cost * $formData[1];

                if(($money = $this->core->module_loader->economy->getMoney($player)) < $cost){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                            RandomUtils::colorMessage("You don't have enough money to purchase this\nYou need: $cost\nYou have: $money."), $this->form_id, MessageForm::METADATA_CLOSE
                        ]);
                        return;
                }

                $this->core->module_loader->economy->subtractMoney($player, $cost);
                $message = $this->core->getMessage('power_ups', 'purchased');
                $message = str_replace(["@name", "@cost", "@time"], [$powerUp[1], $cost, $formData[1]], $message);
                $player->sendMessage(RandomUtils::colorMessage($message));

                $this->core->module_loader->power_ups->setPowerUp($player, $powerUp[0], $formData[1]);
        }
}