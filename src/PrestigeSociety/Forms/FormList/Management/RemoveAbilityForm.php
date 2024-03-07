<?php
namespace PrestigeSociety\Forms\FormList\Management;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\Forms\FormList\Stones\MessageForm;
use PrestigeSociety\UIForms\SimpleForm;

class RemoveAbilityForm extends FormHandler{
        public function send(Player $player){
                $form = new SimpleForm($this);

                $item = $this->getData();
                $itemInHand = $player->getInventory()->getItemInHand();
                $itemName = $itemInHand->getName();

                if(!$item->equalsExact($itemInHand)){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                            "Please do not switch slots while managing item.", $this->form_id, MessageForm::METADATA_CLOSE
                        ]);
                        return;
                }

                $form->setTitle(RandomUtils::colorMessage("&l&8REMOVE ABILITY OF $itemName"));

                $ability = $this->core->module_loader->management->getItemAbility($itemInHand);
                if($ability === null){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                            "This item has no active ability.", $this->form_id, MessageForm::METADATA_CLOSE
                        ]);
                        return;
                }

                $abilityName = $this->core->module_loader->management->abilityIdToName($ability);
                $duration = $this->core->module_loader->management->getItemAbilityDuration($itemInHand);

                $content = "===========================\n";
                $content .= "&7current ability: &f$abilityName\n";
                $content .= "&7ability duration: &f$duration\n";
                $content .= "&l&cARE YOU SURE YOU WANNA REMOVE THIS ITEM'S ABILITY?&r\n";
                $content .= "===========================\n";
                $form->setContent(RandomUtils::colorMessage($content));

                $form->setButton(RandomUtils::colorMessage("&8confirm"));
                $form->setButton(RandomUtils::colorMessage("&8cancel"));

                $this->setData($item);
                $form->send($player);
        }

        public function handleResponse(Player $player, $formData){
                if($formData === 0){
                        $item = $this->core->module_loader->management->setItemAbilityInactive($player->getInventory()->getItemInHand());

                        $player->getInventory()->removeItem($player->getInventory()->getItemInHand());
                        $player->getInventory()->addItem($item);

                        $message = $this->core->getMessage("management", "remove_ability");
                        $message = str_replace("@item", $item->getName(), $message);
                        $player->sendMessage(RandomUtils::colorMessage($message));
                }
        }
}