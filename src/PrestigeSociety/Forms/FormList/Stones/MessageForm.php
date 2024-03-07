<?php
namespace PrestigeSociety\Forms\FormList\Stones;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\UIForms\SimpleForm;
class MessageForm extends FormHandler{
        const METADATA_CLOSE = "meta:close";

        public function send(Player $player){
                $data = $this->getData();

                $form = new SimpleForm($this);
                $form->setTitle(RandomUtils::colorMessage("&4&lERROR"));
                $form->setContent($data[0]);

                if(!in_array(self::METADATA_CLOSE, $data)){
                        $form->setButton(RandomUtils::colorMessage("&8try again"));
                }
                $form->setButton(RandomUtils::colorMessage("&8close"));
                $form->send($player);
        }

        public function handleResponse(Player $player, $formData){
                $data = $this->getData();
                if(in_array(self::METADATA_CLOSE, $data)) return;

                if($formData === 0){
                        $this->core->module_loader->form_manager->sendForm($data[1], $player, $data[2]);
                }
        }
}