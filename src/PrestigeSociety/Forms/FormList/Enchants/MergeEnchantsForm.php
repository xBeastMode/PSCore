<?php
namespace PrestigeSociety\Forms\FormList\Enchants;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\UIForms\CustomForm;
class MergeEnchantsForm extends FormHandler{
        /**
         * @param Player $player
         *
         * @return Item[]
         */
        protected function getEnchantmentBooks(Player $player): array{
                $output_enchantment_books = [];
                $item_in_hand = $player->getInventory()->getItemInHand();

                foreach($player->getInventory()->getContents() as $index => $item){
                        if($index === $player->getInventory()->getHeldItemIndex() || $item->getId() !== ItemIds::ENCHANTED_BOOK) continue;
                        foreach($item->getEnchantments() as $enchantment){
                                if($enchantment->getLevel() < $enchantment->getType()->getMaxLevel() && $item_in_hand->hasEnchantment($enchantment->getType(), $enchantment->getLevel())){
                                        $output_enchantment_books []= $item;
                                }
                        }
                }
                return $output_enchantment_books;
        }

        public function send(Player $player){
                $enchants_cost = $this->core->module_loader->enchants->enchantments_cost;
                $item_in_hand = $player->getInventory()->getItemInHand();

                if($item_in_hand->getId() !== ItemIds::ENCHANTED_BOOK){
                        $message = $this->core->getMessage('enchants', 'cannot_merge_air');
                        $player->sendMessage(RandomUtils::colorMessage($message));
                        return;
                }

                $output_enchantment_books = $this->getEnchantmentBooks($player);
                if(count($output_enchantment_books) <= 0){
                        $message = $this->core->getMessage('enchants', 'not_books_found');
                        $player->sendMessage(RandomUtils::colorMessage($message));
                        return;
                }

                $item_in_hand_enchants = [];
                foreach($item_in_hand->getEnchantments() as $enchantment){
                        $item_in_hand_enchants []= $enchantment->getType()->getName() . " (" . $enchantment->getLevel() . ")";
                }
                $item_in_hand_enchants = implode(", ", $item_in_hand_enchants);

                $form = new CustomForm($this);
                $form->setTitle(RandomUtils::colorMessage("&c&lBOOK BATCH"));
                $content = "===========================\n";
                $content .= "&7Welcome to the book batch!\n\n";
                $content .= "&7The book batch allows you to merge 2 books to create a higher level book.\n";
                $content .= "&7The batch has {$enchants_cost["merge_succeeding_chance"]} percent chance of succeeding.\n";
                $content .= "&7Use the following items to change these chances:\n\n";

                $content .= "&7xp level adds {$enchants_cost["merge_xp_chance_per_level"]} percent chance of succeeding\n\n";

                $content .= "&7per enchantment costs:\n";
                $content .= "&7level II: &f" . $enchants_cost['merge_level_2']. "\n";
                $content .= "&7level III : &f" . $enchants_cost['merge_level_3']. "\n";
                $content .= "&7level IV: &f" . $enchants_cost['merge_level_4']. "\n";
                $content .= "&7level V: &f" . $enchants_cost['merge_level_5']. "\n\n";
                $content .= "&7enchantments found on this item: &f" . $item_in_hand_enchants . "\n";
                $content .= "===========================\n";
                $form->setLabel(RandomUtils::colorMessage($content));

                $book_names = [];
                foreach($output_enchantment_books as $item){
                        $book_names []= $item->getName();
                }

                $available_xp = $player->getXpManager()->getXpLevel();
                $maximum_xp = (int) (100 / (int) $enchants_cost["merge_xp_chance_per_level"]);
                $maximum_xp = $available_xp >= $maximum_xp ? $maximum_xp : $available_xp;

                $this->setVar(EnchantingFormData::OUTPUT_ENCHANTMENT_BOOKS, $output_enchantment_books);
                $this->setVar(EnchantingFormData::ITEM_IN_HAND_SLOT_INDEX, $player->getInventory()->getHeldItemIndex());

                $form->setDropdown(RandomUtils::colorMessage("&7book to merge with"), $book_names, 0);
                $form->setSlider(RandomUtils::colorMessage("&7xp increase"), 0, $available_xp > 0 ? $maximum_xp : 0, $available_xp > 0 ? 1 : 0, 0);
                $form->send($player);
        }

        public function handleResponse(Player $player, $formData){
                if($this->getVar(EnchantingFormData::ITEM_IN_HAND_SLOT_INDEX) !== $player->getInventory()->getHeldItemIndex()){
                        $message = $this->core->getMessage('enchants', 'cannot_apply_air');
                        $player->sendMessage(RandomUtils::colorMessage($message));
                        return;
                }

                $xp_amount = $formData[2];

                $enchants_cost = $this->core->module_loader->enchants->enchantments_cost;
                $enchanting_shard_chance_increase = $enchants_cost["merge_succeeding_chance"] + ($formData[2] * ((float)$enchants_cost["merge_xp_chance_per_level"]));

                if($enchanting_shard_chance_increase > 100){
                        $enchanting_shard_chance_increase = 100;
                }

                $handler = $this->core->module_loader->form_manager->getHandler($this->core->module_loader->enchants->CONFIRM_MERGE_ID);

                $handler->setVar(EnchantingFormData::ITEM_IN_HAND, $player->getInventory()->getItemInHand());
                $handler->setVar(EnchantingFormData::SELECTED_BOOK, $this->getVar(EnchantingFormData::OUTPUT_ENCHANTMENT_BOOKS)[$formData[1]]);
                $handler->setVar(EnchantingFormData::SELECTED_XP_AMOUNT, $xp_amount);
                $handler->setVar(EnchantingFormData::ENCHANTING_SHARD_INCREASE, $enchanting_shard_chance_increase);
                $handler->setVar(EnchantingFormData::ITEM_IN_HAND_SLOT_INDEX, $player->getInventory()->getHeldItemIndex());

                $handler->send($player);
        }
}