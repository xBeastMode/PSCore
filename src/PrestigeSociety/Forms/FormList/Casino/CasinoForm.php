<?php
namespace PrestigeSociety\Forms\FormList\Casino;
use DateTime;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\UIForms\SimpleForm;
class CasinoForm extends FormHandler{
        public function send(Player $player){
                $username = $player->getName();
                $form = new SimpleForm($this);
                $form->setTitle(RandomUtils::colorMessage("&8&lCASINO"));
                $content = "===========================\n";
                $content .= "&7Hey there, &f{$username}&7!\n";
                $content .= "&7Welcome to the casino!\n\n";
                $content .= "&7Play slot machines and cash flip!\n";
                $content .= "===========================\n";
                $form->setContent(RandomUtils::colorMessage($content));
                $form->setButton(RandomUtils::colorMessage("&8slot machine\n&7- &8play different slot machines! &7-"));
                $form->setButton(RandomUtils::colorMessage("&8cash flip\n&7- &8more cash is bigger price! &7-"));
                $form->send($player);
        }

        public function handleResponse(Player $player, $formData){
                if($formData === 0){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->casino->SLOT_MACHINES_ID, $player);
                }else{
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->casino->CASH_FLIP_ID, $player);
                }
        }
}