<?php
namespace PrestigeSociety\Forms\FormList\Enchants;
use pocketmine\item\Item;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Enchants\Enchants;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\Forms\FormList\Stones\MessageForm;
use PrestigeSociety\UIForms\SimpleForm;
class ConfirmApplyForm extends FormHandler{
        /**
         * @return Item
         */
        protected function getItem(): Item{
                /** @var Item $item_in_hand */
                $item_in_hand = $this->getVar(EnchantingFormData::ITEM_IN_HAND);
                /** @var Item $target */
                $selected_item = clone $this->getVar(EnchantingFormData::SELECTED_ITEM);

                foreach($item_in_hand->getEnchantments() as $enchantment){
                        $selected_item->addEnchantment($enchantment);
                }

                return $selected_item;
        }

        protected function getCost(){
                /** @var Item $item_in_hand */
                $item_in_hand = $this->getVar(EnchantingFormData::ITEM_IN_HAND);
                $costs = $this->core->module_loader->enchants->enchantments_cost;

                $cost = 0;

                foreach($item_in_hand->getEnchantments() as $enchantment){
                        $name = $enchantment->getType()->getName();
                        $name = str_replace(" ", "_", strtolower(Enchants::VANILLA_CONVERTER[$name] ?? $name));
                        $cost += ((int) $costs[$name]) * $enchantment->getLevel();
                }

                return $cost;
        }

        public function send(Player $player){
                $total_cost = $this->getCost();
                $final_output_item = $this->getItem();

                $enchanting_shard_chance_increase = $this->getVar(EnchantingFormData::ENCHANTING_SHARD_INCREASE);
                $enchanting_shard_chance_decrease = $this->getVar(EnchantingFormData::ENCHANTING_SHARD_DECREASE);

                $this->setVar(EnchantingFormData::TOTAL_COST, $total_cost);
                $this->setVar(EnchantingFormData::FINAL_OUTPUT_ITEM, $final_output_item);

                $final_enchantments = [];
                foreach($final_output_item->getEnchantments() as $enchantment){
                        $final_enchantments []= $enchantment->getType()->getName() . " (" . $enchantment->getLevel() . ")";
                }

                $final_enchantments = implode(", ", $final_enchantments);

                $form = new SimpleForm($this);
                $form->setTitle(RandomUtils::colorMessage("&c&lCONFIRM ASSEMBLE"));
                $content = "===========================\n";
                $content .= "&7total cost: &f$total_cost\n";
                $content .= "&7final enchantments: &f$final_enchantments\n";
                $content .= "&7success chance: &f$enchanting_shard_chance_increase percent\n";
                $content .= "&7breaking chance: &f$enchanting_shard_chance_decrease percent\n";
                $content .= "===========================\n";
                $form->setContent(RandomUtils::colorMessage($content));
                $form->setButton(RandomUtils::colorMessage("&8batch"));
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

                        /** @var Item[] $items */
                        $items = [
                            $this->getVar(EnchantingFormData::ITEM_IN_HAND),
                            $this->getVar(EnchantingFormData::SELECTED_ITEM_INDEX)
                        ];

                        $enchanting_shard_chance_increase = $this->getVar(EnchantingFormData::ENCHANTING_SHARD_INCREASE);
                        $enchanting_shard_chance_decrease = $this->getVar(EnchantingFormData::ENCHANTING_SHARD_DECREASE);

                        if(($money = $this->core->module_loader->economy->getMoney($player)) < $total_cost){
                                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                                    RandomUtils::colorMessage("You don't have enough money to purchase this\nYou need: $total_cost\nYou have: $money."), $this->form_id, MessageForm::METADATA_CLOSE
                                ]);
                                return;
                        }

                        /** @var Item[][]|int[][] $enchanting_shard_output */
                        $enchanting_shard_output = $this->getVar(EnchantingFormData::ENCHANTING_SHARD_OUTPUT);

                        foreach($enchanting_shard_output as $datum){
                                $datum[0]->setCount($datum[1]);
                                $player->getInventory()->removeItem($datum[0]);
                        }

                        $player->getXpManager()->setXpLevel($player->getXpManager()->getXpLevel() - $this->getVar(EnchantingFormData::SELECTED_XP_AMOUNT));
                        $this->core->module_loader->economy->subtractMoney($player, $total_cost);

                        if(RandomUtils::randomFloat(0, 100) <= $enchanting_shard_chance_decrease){
                                RandomUtils::playSound("random.break", $player);

                                $player->getInventory()->removeItem($items[0]);
                                $player->sendTip(RandomUtils::colorMessage("&4&lBOOK WAS BROKEN"));

                                $message = $this->core->getMessage('enchants', 'apply_fail');
                                $player->sendMessage(RandomUtils::colorMessage($message));
                        }else{
                                if(RandomUtils::randomFloat(0, 100) <= $enchanting_shard_chance_increase){
                                        $player->getInventory()->clear($this->getVar(EnchantingFormData::SELECTED_ITEM_INDEX));
                                        $player->getInventory()->removeItem($items[0]);
                                        $player->getInventory()->addItem($final_output_item);

                                        RandomUtils::playSound("random.anvil_use", $player);

                                        $message = $this->core->getMessage('enchants', 'apply_success');
                                        $player->sendMessage(RandomUtils::colorMessage($message));
                                }else{

                                        RandomUtils::playSound("random.break", $player);

                                        $message = $this->core->getMessage('enchants', 'apply_fail');
                                        $player->sendMessage(RandomUtils::colorMessage($message));
                                }
                        }
                }else{
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->enchants->ENCHANTS_ID, $player);
                }
        }
}