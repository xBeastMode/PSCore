<?php
namespace PrestigeSociety\Forms\FormList\Management;
use pocketmine\item\Item;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\Forms\FormList\Stones\MessageForm;
use PrestigeSociety\UIForms\SimpleForm;
class AddSlotForm extends FormHandler{
        public function send(Player $player){
                $form = new SimpleForm($this);

                /** @var Item $item */
                $item = $this->getData();
                $itemInHand = $player->getInventory()->getItemInHand();
                $itemName = $itemInHand->getName();

                if(!$item->equalsExact($itemInHand)){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                            "Please do not switch slots while managing item.", $this->form_id, MessageForm::METADATA_CLOSE
                        ]);
                        return;
                }

                $form->setTitle(RandomUtils::colorMessage("&l&8ADD SLOT"));

                $maxSlots = $this->core->module_loader->management->getItemMaxSlots($item);
                $slotsUsed = count($itemInHand->getEnchantments());

                if($slotsUsed >= $maxSlots){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                            "All slots used for this item.", $this->form_id, MessageForm::METADATA_CLOSE
                        ]);
                        return;
                }

                $content = "===========================\n";
                $content .= "&7item: &f$itemName\n";
                $content .= "&7max slots: &f$maxSlots\n";
                $content .= "&7slots used: &f$slotsUsed\n";
                $content .= "===========================\n";
                $form->setContent(RandomUtils::colorMessage($content));

                $form->setButton(RandomUtils::colorMessage("&8custom"));
                $form->setButton(RandomUtils::colorMessage("&8vanilla"));

                $this->setData($itemInHand);
                $form->send($player);
        }

        public function handleResponse(Player $player, $formData){
                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->management->CONFIRM_ADD_SLOT_ID, $player, [
                    $this->getData(),
                    ["custom", "vanilla"][$formData]
                ]);
        }
}