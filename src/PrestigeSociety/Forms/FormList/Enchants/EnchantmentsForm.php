<?php
namespace PrestigeSociety\Forms\FormList\Enchants;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\UIForms\SimpleForm;
class EnchantmentsForm extends FormHandler{
        public function send(Player $player){
                $username = $player->getName();
                $form = new SimpleForm($this);
                $form->setTitle(RandomUtils::colorMessage("&c&lENCHANTMENTS"));
                $content = "===========================\n";
                $content .= "&7Hey there, &b{$username}&7!\n\n";
                $content .= "&7Welcome to the enchantments!\n";
                $content .= "&7- Book facsimile creates a copy of your book.\n\n";
                $content .= "&7- Book batcher is used to merge books to create higher level books.\n\n";
                $content .= "&7- Book assembler is used to apply books to your items.\n\n";
                $content .= "&7- Enchantment abstract is used to remove enchantments from your items.\n";
                $content .= "===========================\n";
                $form->setContent(RandomUtils::colorMessage($content));
                //$form->setButton(RandomUtils::colorMessage("&8vanilla enchantments"));
                $form->setButton(RandomUtils::colorMessage("&8book facsimile"));
                $form->setButton(RandomUtils::colorMessage("&8book batcher"));
                $form->setButton(RandomUtils::colorMessage("&8book assembler"));
                $form->setButton(RandomUtils::colorMessage("&8enchantment abstract"));
                $form->send($player);
        }

        public function handleResponse(Player $player, $formData){
                $forms = [
                    $this->core->module_loader->enchants->REPLICATE_ENCHANT_ID,
                    $this->core->module_loader->enchants->MERGE_ENCHANTS_ID,
                    $this->core->module_loader->enchants->APPLY_ENCHANTS_ID,
                    $this->core->module_loader->enchants->REMOVE_ENCHANT_ID
                ];

                $handler = $this->core->module_loader->form_manager->sendForm($forms[$formData], $player);
                $handler->setVar(EnchantingFormData::ITEM_IN_HAND_SLOT_INDEX, $player->getInventory()->getHeldItemIndex());
        }
}