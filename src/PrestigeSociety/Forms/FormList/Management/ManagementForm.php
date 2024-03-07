<?php
namespace PrestigeSociety\Forms\FormList\Management;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\Forms\FormList\Stones\MessageForm;
use PrestigeSociety\UIForms\SimpleForm;
class ManagementForm extends FormHandler{
        public function send(Player $player){
                $form = new SimpleForm($this);

                $item = $player->getInventory()->getItemInHand();

                if($item->isNull()){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                            "Air is not allowed.", $this->form_id, MessageForm::METADATA_CLOSE
                        ]);
                        return;
                }

                $itemName = strtoupper($item->getName());
                $form->setTitle(RandomUtils::colorMessage("&l&8$itemName MANAGEMENT"));

                $username = $player->getName();
                $content = "===========================\n";
                $content .= "&7Hey there, &f{$username}&7!\n";
                $content .= "&7Welcome to item management!\n\n";
                $content .= "&7Here you can manage your item!\n";
                $content .= "===========================\n";
                $form->setContent(RandomUtils::colorMessage($content));

                $form->setButton(RandomUtils::colorMessage("&8manage slots"));
                $form->setButton(RandomUtils::colorMessage("&8manage ability"));

                $this->setData($item);
                $form->send($player);
        }

        public function handleResponse(Player $player, $formData){
                $forms = [
                    $this->core->module_loader->management->MANAGE_SLOTS_ID,
                    $this->core->module_loader->management->MANAGE_ABILITY_ID,
                ];

                $this->core->module_loader->form_manager->sendForm($forms[$formData], $player, $this->getData());
        }
}