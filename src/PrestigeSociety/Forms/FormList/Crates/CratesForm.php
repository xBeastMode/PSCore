<?php
namespace PrestigeSociety\Forms\FormList\Crates;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\UIForms\SimpleForm;
class CratesForm extends FormHandler{
        public function send(Player $player){
                $username = $player->getName();
                $form = new SimpleForm($this);
                $form->setTitle(RandomUtils::colorMessage("&8&lCRATES"));
                $content = "===========================\n";
                $content .= "&7Hey there, &f{$username}&7!\n";
                $content .= "&7Welcome to the crates!\n\n";
                $content .= "&7Here you can manage your crates!\n";
                $content .= "===========================\n";
                $form->setContent(RandomUtils::colorMessage($content));
                $form->setButton(RandomUtils::colorMessage("&8open crates"));
                $form->setButton(RandomUtils::colorMessage("&8buy crates"));
                $form->send($player);
        }

        public function handleResponse(Player $player, $formData){
                if($formData === 0){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->crates->OPEN_CRATES_ID, $player);
                }else{
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->crates->BUY_CRATES_ID, $player);
                }
        }
}