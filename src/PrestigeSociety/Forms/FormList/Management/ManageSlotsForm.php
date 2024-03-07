<?php
namespace PrestigeSociety\Forms\FormList\Management;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\Forms\FormList\Stones\MessageForm;
use PrestigeSociety\UIForms\SimpleForm;
class ManageSlotsForm extends FormHandler{
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

                $form->setTitle(RandomUtils::colorMessage("&l&8MANAGE SLOTS OF $itemName"));

                $maxSlots = $this->core->module_loader->management->getItemMaxSlots($item);
                $slotsUsed = count($itemInHand->getEnchantments());

                $slots = array_map(function (EnchantmentInstance $instance){
                        return RandomUtils::getNameFromTranslatable($instance) . " (level " . $instance->getLevel() . " of " . $instance->getType()->getMaxLevel() . ")";
                }, $itemInHand->getEnchantments());

                $content = "===========================\n";
                $content .= "&7max slots: &f$maxSlots\n";
                $content .= "&7slots used: &f$slotsUsed\n";
                $content .= "===========================\n";
                $form->setContent(RandomUtils::colorMessage($content));

                $form->setButton(RandomUtils::colorMessage("&8add slot"));
                $form->setButton(RandomUtils::colorMessage("&8unlock slot"));
                foreach($slots as $slot){
                        $form->setButton(RandomUtils::colorMessage("&8$slot"));
                }

                $this->setData($itemInHand);
                $form->send($player);
        }

        public function handleResponse(Player $player, $formData){
                if($formData === 0){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->management->ADD_SLOT_ID, $player, $this->getData());
                }else if($formData === 1){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->management->UNLOCK_SLOT_ID, $player, $this->getData());
                }else{
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->management->VIEW_SLOT_ID, $player, [$this->getData(), $formData - 2]);
                }
        }
}