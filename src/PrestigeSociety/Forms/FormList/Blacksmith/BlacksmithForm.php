<?php
namespace PrestigeSociety\Forms\FormList\Blacksmith;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\Forms\FormList\Stones\MessageForm;
use PrestigeSociety\UIForms\SimpleForm;
class BlacksmithForm extends FormHandler{
        public function send(Player $player){
                $item = $player->getInventory()->getItemInHand();

                if($item->isNull()){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                            "Air is not allowed.", $this->form_id, MessageForm::METADATA_CLOSE
                        ]);
                        return;
                }

                $username = $player->getName();
                $form = new SimpleForm($this);
                $form->setTitle(RandomUtils::colorMessage("&8&lBLACKSMITH"));
                $content = "===========================\n";
                $content .= "&7Hey there, &f{$username}&7!\n";
                $content .= "&7Welcome to the blacksmith!\n\n";
                $content .= "&7You can rename and repair items.\n";
                $content .= "===========================\n";
                $form->setContent(RandomUtils::colorMessage($content));
                $form->setButton(RandomUtils::colorMessage("&8rename"));
                $form->setButton(RandomUtils::colorMessage("&8repair"));
                $form->send($player);
        }

        public function handleResponse(Player $player, $formData){
                $this->core->module_loader->form_manager->sendForm($this->getData()[$formData], $player, $this->getData()[2]);
        }
}