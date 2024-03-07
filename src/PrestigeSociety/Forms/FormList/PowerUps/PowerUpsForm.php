<?php
namespace PrestigeSociety\Forms\FormList\PowerUps;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\UIForms\SimpleForm;
class PowerUpsForm extends FormHandler{
        public function send(Player $player){
                $username = $player->getName();
                $form = new SimpleForm($this);
                $form->setTitle(RandomUtils::colorMessage("&8&lPOWER-UPS"));
                $content = "===========================\n";
                $content .= "&7Hey there, &f{$username}&7!\n\n";
                $content .= "&7Welcome to the power up menu!\n";
                $content .= "&7You can activate power-ups!\n";
                $content .= "===========================\n";
                $form->setContent(RandomUtils::colorMessage($content));
                //$form->setButton(RandomUtils::colorMessage("&8buy"));
                $form->setButton(RandomUtils::colorMessage("&8activate"));
                $form->send($player);
        }

        public function handleResponse(Player $player, $formData){
                /*if($formData === 0){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->power_ups->BUY_POWER_UPS_ID, $player);
                }else{
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->power_ups->ACTIVATE_POWER_UPS_ID, $player);
                }*/

                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->power_ups->ACTIVATE_POWER_UPS_ID, $player);
        }
}