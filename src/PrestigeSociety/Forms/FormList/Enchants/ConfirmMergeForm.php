<?php
namespace PrestigeSociety\Forms\FormList\Enchants;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\Forms\FormList\Stones\MessageForm;
use PrestigeSociety\UIForms\SimpleForm;
class ConfirmMergeForm extends FormHandler{
        /**
         * @return Item
         */
        protected function mergeEnchantmentBooks(): Item{
                $item = ItemFactory::getInstance()->get(ItemIds::ENCHANTED_BOOK);
                /** @var Item $item_1 */
                $item_1 = $this->getVar(EnchantingFormData::ITEM_IN_HAND);
                /** @var Item $item_2 */
                $item_2 = $this->getVar(EnchantingFormData::SELECTED_BOOK);

                $item_most_enchantments = count($item_1->getEnchantments()) > count($item_2->getEnchantments()) ? $item_1 : $item_2;
                $item_least_enchantments = count($item_1->getEnchantments()) > count($item_2->getEnchantments()) ? $item_2 : $item_1;

                $lore = [];

                foreach($item_most_enchantments->getEnchantments() as $enchantment){
                        if($item_least_enchantments->hasEnchantment($enchantment->getType(), $enchantment->getLevel())){
                                $item->addEnchantment(new EnchantmentInstance($enchantment->getType(), $enchantment->getLevel() + 1));
                        }
                }

                $lore []= RandomUtils::colorMessage("&r&7go to &6ENCHANTER &7to use");

                $item->setLore($lore);
                $item->setCustomName(RandomUtils::colorMessage("&c&lBATCHED BOOK"));

                return $item;
        }

        /**
         * @return int
         */
        protected function getMergeCost(): int{
                $enchants_cost = $this->core->module_loader->enchants->enchantments_cost;
                $total_cost = 0;

                /** @var Item $item_1 */
                $item_1 = $this->getVar(EnchantingFormData::ITEM_IN_HAND);
                /** @var Item $item_2 */
                $item_2 = $this->getVar(EnchantingFormData::SELECTED_BOOK);

                $item_most_enchantments = count($item_1->getEnchantments()) > count($item_2->getEnchantments()) ? $item_1 : $item_2;
                $item_least_enchantments = count($item_1->getEnchantments()) > count($item_2->getEnchantments()) ? $item_2 : $item_1;

                foreach($item_most_enchantments->getEnchantments() as $enchantment){
                        if($item_least_enchantments->hasEnchantment($enchantment->getType(), $level = $enchantment->getLevel())){
                                $nextLevel = $level + 1;
                                $total_cost += (int) $enchants_cost["merge_level_" . $nextLevel];
                        }
                }

                return $total_cost;

        }

        public function send(Player $player){
                $total_cost = $this->getMergeCost();
                $final_output_item = $this->mergeEnchantmentBooks();

                $this->setVar(EnchantingFormData::TOTAL_COST, $total_cost);
                $this->setVar(EnchantingFormData::FINAL_OUTPUT_ITEM, $final_output_item);

                $enchanting_shard_chance_increase = $this->getVar(EnchantingFormData::ENCHANTING_SHARD_INCREASE);

                $final_enchantments = [];
                foreach($final_output_item->getEnchantments() as $enchantment){
                        $final_enchantments []= $enchantment->getType()->getName() . " (" . $enchantment->getLevel() . ")";
                }
                $final_enchantments = implode(", ", $final_enchantments);

                $form = new SimpleForm($this);
                $form->setTitle(RandomUtils::colorMessage("&c&lCONFIRM BATCH"));
                $content = "===========================\n";
                $content .= "&7total cost: &f$total_cost\n";
                $content .= "&7final enchantments: &f$final_enchantments\n";
                $content .= "&7success chance: &f$enchanting_shard_chance_increase percent\n";
                $content .= "===========================\n";
                $form->setContent(RandomUtils::colorMessage($content));
                $form->setButton(RandomUtils::colorMessage("&8finalize batch"));
                $form->setButton(RandomUtils::colorMessage("&8cancel"));
                $form->send($player);
        }

        public function handleResponse(Player $player, $formData){
                if($formData === 0){
                        if($this->getVar(EnchantingFormData::ITEM_IN_HAND_SLOT_INDEX) !== $player->getInventory()->getHeldItemIndex()){
                                $message = $this->core->getMessage('enchants', 'cannot_apply_air');
                                $player->sendMessage(RandomUtils::colorMessage($message));
                                return;
                        }

                        $total_cost = $this->getVar(EnchantingFormData::TOTAL_COST);
                        $final_output_item = $this->getVar(EnchantingFormData::FINAL_OUTPUT_ITEM);

                        /** @var Item $item_1 */
                        $item_1 = $this->getVar(EnchantingFormData::ITEM_IN_HAND);
                        /** @var Item $item_2 */
                        $item_2 = $this->getVar(EnchantingFormData::SELECTED_BOOK);

                        $items = [$item_1, $item_2];
                        $enchanting_shard_chance_increase = $this->getVar(EnchantingFormData::ENCHANTING_SHARD_INCREASE);

                        if(($money = $this->core->module_loader->economy->getMoney($player)) < $total_cost){
                                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                                    RandomUtils::colorMessage("You don't have enough money to purchase this\nYou need: $total_cost\nYou have: $money."), $this->form_id, MessageForm::METADATA_CLOSE
                                ]);
                                return;
                        }

                        $player->getXpManager()->setXpLevel($player->getXpManager()->getXpLevel() - $this->getVar(EnchantingFormData::SELECTED_XP_AMOUNT));
                        $this->core->module_loader->economy->subtractMoney($player, $total_cost);

                        if(RandomUtils::randomFloat(0, 100) <= $enchanting_shard_chance_increase){
                                $player->getInventory()->removeItem(...$items);
                                $player->getInventory()->addItem($final_output_item);

                                RandomUtils::playSound("random.anvil_use", $player);

                                $message = $this->core->getMessage('enchants', 'merge_success');
                                $player->sendMessage(RandomUtils::colorMessage($message));
                        }else{
                                RandomUtils::playSound("random.break", $player);

                                $message = $this->core->getMessage('enchants', 'merge_fail');
                                $player->sendMessage(RandomUtils::colorMessage($message));
                        }
                }else{
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->enchants->ENCHANTS_ID, $player);
                }
        }
}