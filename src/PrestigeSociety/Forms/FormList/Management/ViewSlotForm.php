<?php
namespace PrestigeSociety\Forms\FormList\Management;
use pocketmine\item\Item;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\Forms\FormList\Stones\MessageForm;
use PrestigeSociety\UIForms\SimpleForm;
class ViewSlotForm extends FormHandler{
        public function send(Player $player){
                $form = new SimpleForm($this);

                /** @var Item $item */
                $item = $this->getData()[0];

                $itemInHand = $player->getInventory()->getItemInHand();
                $itemName = $itemInHand->getName();

                if(!$item->equalsExact($itemInHand)){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                            "Please do not switch slots while managing item.", $this->form_id, MessageForm::METADATA_CLOSE
                        ]);
                        return;
                }

                $enchantmentIndex = $this->getData()[1];
                $enchantment = array_values($itemInHand->getEnchantments())[$enchantmentIndex];

                $form->setTitle(RandomUtils::colorMessage("&l&8MANAGE ENCHANTMENTS"));
                $name = RandomUtils::getNameFromTranslatable($enchantment);

                $content = "===========================\n";
                $content .= "&7item: &f$itemName\n";
                $content .= "&7enchantment: &f{$name}\n";
                $content .= "===========================\n";
                $form->setContent(RandomUtils::colorMessage($content));

                $form->setButton(RandomUtils::colorMessage("&8upgrade"));
                $form->setButton(RandomUtils::colorMessage("&8remove"));

                $form->send($player);
        }

        public function handleResponse(Player $player, $formData){
                $forms = [
                    $this->core->module_loader->management->UPGRADE_SLOT_ID,
                    $this->core->module_loader->management->REMOVE_SLOT_ID,
                ];

                $this->core->module_loader->form_manager->sendForm($forms[$formData], $player, $this->getData());
        }
}