<?php
namespace PrestigeSociety\Forms\FormList\Enchants;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\UIForms\CustomForm;
class ReplicateEnchantForm extends FormHandler{
        public function send(Player $player){
                $item_in_hand = $player->getInventory()->getItemInHand();
                $enchants_cost = $this->core->module_loader->enchants->enchantments_cost;

                if($item_in_hand->getId() !== ItemIds::ENCHANTED_BOOK){
                        $message = $this->core->getMessage('enchants', 'cannot_apply_air');
                        $player->sendMessage(RandomUtils::colorMessage($message));
                        return;
                }

                if(count($item_in_hand->getEnchantments()) <= 0){
                        $message = $this->core->getMessage('enchants', 'no_apply_enchants_found');
                        $player->sendMessage(RandomUtils::colorMessage($message));
                        return;
                }

                /** @var Item[][]|int[][] $enchanting_shard */
                $enchanting_shard = [];
                $indexes = 0;

                $form = new CustomForm($this);
                $form->setTitle(RandomUtils::colorMessage("&c&lBOOK FACSIMILE"));
                $content = "===========================\n";
                $content .= "&7Welcome to the book facsimile!\n\n";
                $content .= "&7The facsimile is used to create a copy of your book.\n";
                $content .= "&7The facsimile has {$enchants_cost["replicate_succeeding_chance"]} percent chance of succeeding.\n";
                $content .= "&7The book also has {$enchants_cost["replicate_breaking_chance"]} percent chance of breaking if the facsimile fails.\n";
                $content .= "&7Use the following items to change these chances:\n\n";

                $content .= "&7xp level adds {$enchants_cost["replicate_xp_chance_per_level"]} percent chance of succeeding\n";

                foreach($enchants_cost["replicate"] as $merge){
                        $content .= "&7{$merge["name"]}&r&7 reduces {$merge["worth"]}% chance of breaking\n";
                        $item = RandomUtils::parseItemsWithEnchantments([$merge["item"]])[0];

                        foreach($player->getInventory()->getContents() as $cnt){
                                $hash = $item->getName() . ":" . $cnt->getId() . ":" . $item->getDamage();

                                if($item->getName() === $cnt->getName() && $item->equals($cnt, true, false)){
                                        if(isset($enchanting_shard[$hash])){
                                                $enchanting_shard[$hash][1] += $cnt->getCount();
                                        }else{
                                                $enchanting_shard[$hash][0] = $cnt;
                                                $enchanting_shard[$hash][1] = $cnt->getCount();
                                                $enchanting_shard[$hash][2] = (float) $merge["worth"];
                                                $enchanting_shard[$indexes++] = $hash;
                                        }
                                }
                        }
                }

                $content .= "===========================\n";

                $available_xp = $player->getXpManager()->getXpLevel();
                $maximum_xp = (int) (100 / (int) $enchants_cost["replicate_xp_chance_per_level"]);
                $maximum_xp = $available_xp >= $maximum_xp ? $maximum_xp : $available_xp;

                $form->setLabel(RandomUtils::colorMessage($content));
                $form->setSlider(RandomUtils::colorMessage("&7xp increase"), 0, $available_xp > 0 ? $maximum_xp : 0, $available_xp > 0 ? 1 : 0, 0);

                if(count($enchanting_shard) > 0){
                        foreach($enchanting_shard as $index => $item){
                                if(is_int($index)) continue;
                                $max = (int)((int) $enchants_cost["replicate_breaking_chance"] / $item[2]);
                                $max = $item[1] >= $max ? $max : $item[1];

                                $form->setSlider($item[0]->getName(), 0, $max, 1, 0);
                        }
                }

                $this->setVar(EnchantingFormData::ENCHANTING_SHARD_USED, $enchanting_shard);
                $this->setVar(EnchantingFormData::ITEM_IN_HAND_SLOT_INDEX, $player->getInventory()->getHeldItemIndex());

                $form->send($player);
        }

        public function handleResponse(Player $player, $formData){
                if($this->getVar(EnchantingFormData::ITEM_IN_HAND_SLOT_INDEX) !== $player->getInventory()->getHeldItemIndex()){
                        $message = $this->core->getMessage('enchants', 'cannot_apply_air');
                        $player->sendMessage(RandomUtils::colorMessage($message));
                        return;
                }

                $enchants_cost = $this->core->module_loader->enchants->enchantments_cost;

                array_shift($formData);
                $xp_amount = array_shift($formData);
                $enchanting_shard_chance_increase = $enchants_cost["replicate_succeeding_chance"] + ($xp_amount * ((float)$enchants_cost["replicate_xp_chance_per_level"]));
                $enchanting_shard_chance_decrease = (float)$enchants_cost["replicate_breaking_chance"];

                if($enchanting_shard_chance_increase > 100){
                        $enchanting_shard_chance_increase = 100;
                }

                $enchanting_shard_output = [];

                $enchanting_shard = $this->getVar(EnchantingFormData::ENCHANTING_SHARD_USED);
                foreach($formData as $index => $datum){
                        if(isset($enchanting_shard[$index])){
                                $hash = $enchanting_shard[$index];
                                $data = $enchanting_shard[$hash];

                                $enchanting_shard_output [] = [$data[0], $datum]; // item, item count
                                $enchanting_shard_chance_decrease -= $data[2] * $datum; // chance reduction * item count

                                if($enchanting_shard_chance_decrease < 0){
                                        $enchanting_shard_chance_decrease = 0;
                                }
                        }
                }

                $handler = $this->core->module_loader->form_manager->getHandler($this->core->module_loader->enchants->CONFIRM_REPLICATE_ID);

                $handler->setVar(EnchantingFormData::ITEM_IN_HAND, $player->getInventory()->getItemInHand());
                $handler->setVar(EnchantingFormData::ENCHANTING_SHARD_INCREASE, $enchanting_shard_chance_increase);
                $handler->setVar(EnchantingFormData::ENCHANTING_SHARD_DECREASE, $enchanting_shard_chance_decrease);
                $handler->setVar(EnchantingFormData::ENCHANTING_SHARD_OUTPUT, $enchanting_shard_output);
                $handler->setVar(EnchantingFormData::SELECTED_XP_AMOUNT, $xp_amount);
                $handler->setVar(EnchantingFormData::ITEM_IN_HAND_SLOT_INDEX, $player->getInventory()->getHeldItemIndex());

                $handler->send($player);
        }
}