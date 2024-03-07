<?php
namespace PrestigeSociety\Forms\FormList\Kits;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\UIForms\SimpleForm;
class ChooseKitForm extends FormHandler{
        public function send(Player $player){
                $form = new SimpleForm($this);
                $kits = $this->core->module_loader->kits->getKitNames();

                $form->setTitle(RandomUtils::colorMessage("&l&8CHOOSE KIT"));
                foreach($kits as $kit){
                        $form->setButton(RandomUtils::colorMessage("&l&8$kit"));
                }

                $this->setData($kits);
                $form->send($player);
        }

        public function handleResponse(Player $player, $formData){
                $kits = $this->getData();
                $kit = $kits[$formData];

                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->kits->CHOOSE_KIT_OPTION_ID, $player, $kit);
        }
}