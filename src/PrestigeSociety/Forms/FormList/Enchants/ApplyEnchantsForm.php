<?php
namespace PrestigeSociety\Forms\FormList\Enchants;
use DaPigGuy\PiggyCustomEnchants\CustomEnchantManager;
use DaPigGuy\PiggyCustomEnchants\utils\Utils;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\UIForms\CustomForm;
class ApplyEnchantsForm extends FormHandler{

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

                /** @var Item[] $items */
                $items = [];
                $item_indexes = [];
                foreach($player->getInventory()->getContents() as $index => $item){
                        if($item->getId() === ItemIds::ENCHANTED_BOOK) continue;

                        foreach($item_in_hand->getEnchantments() as $enchantment){
                                if($item->hasEnchantment($enchantment->getType())) continue;

                                $ce = CustomEnchantManager::getEnchantment($enchantment->getType()->getId());
                                if($ce === null){
                                        $items []= $item;
                                        $item_indexes []= $index;
                                }else{
                                        if(!Utils::itemMatchesItemType($item, $ce->getItemType())) continue;
                                        $items []= $item;
                                        $item_indexes []= $index;
                                }
                        }
                }

                if(count($items) <= 0){
                        $message = $this->core->getMessage('enchants', 'no_compatible_items');
                        $player->sendMessage(RandomUtils::colorMessage($message));
                        return;
                }

                $names = [];
                foreach($items as $item){
                        $names []= $item->getName();
                }

                /** @var Item[][]|int[][] $enchanting_shard */
                $enchanting_shard = [];
                $indexes = 0;

                $form = new CustomForm($this);
                $form->setTitle(RandomUtils::colorMessage("&c&lBOOK ASSEMBLER"));
                $content = "===========================\n";
                $content .= "&7Welcome to the book assembler!\n\n";
                $content .= "&7The assembler is used to apply books to your items.\n";
                $content .= "&7The assembler has {$enchants_cost["apply_succeeding_chance"]} percent chance of succeeding.\n";
                $content .= "&7The book also has {$enchants_cost["apply_breaking_chance"]} percent chance of breaking if the assemble fails.\n";
                $content .= "&7Use the following items to change these chances:\n\n";

                $content .= "&7xp level adds {$enchants_cost["apply_xp_chance_per_level"]} percent chance of succeeding\n";

                foreach($enchants_cost["apply"] as $shard_data){
                        $content .= "&7{$shard_data["name"]}&r&7 reduces {$shard_data["worth"]} percent chance of breaking\n";
                        $shard_item = RandomUtils::parseItemsWithEnchantments([$shard_data["item"]])[0];

                        foreach($player->getInventory()->getContents() as $inventory_item){
                                $hash = $shard_item->getName() . ":" . $inventory_item->getId() . ":" . $shard_item->getDamage();

                                if($shard_item->getName() === $inventory_item->getName() && $shard_item->equals($inventory_item, true, false)){
                                        if(isset($enchanting_shard[$hash])){
                                                $enchanting_shard[$hash][1] += $inventory_item->getCount();
                                        }else{
                                                $enchanting_shard[$hash][0] = $inventory_item;
                                                $enchanting_shard[$hash][1] = $inventory_item->getCount();
                                                $enchanting_shard[$hash][2] = (float) $shard_data["worth"];
                                                $enchanting_shard[$indexes++] = $hash;
                                        }
                                }
                        }
                }

                $content .= "===========================\n";

                $available_xp = $player->getXpManager()->getXpLevel();
                $maximum_xp = (int) (100 / (int) $enchants_cost["apply_xp_chance_per_level"]);
                $maximum_xp = $available_xp >= $maximum_xp ? $maximum_xp : $available_xp;

                $form->setLabel(RandomUtils::colorMessage($content));
                $form->setDropdown(RandomUtils::colorMessage("&7item to apply to"), $names, 0);
                $form->setSlider(RandomUtils::colorMessage("&7xp increase"), 0, $available_xp > 0 ? $maximum_xp : 0, $available_xp > 0 ? 1 : 0, 0);

                if(count($enchanting_shard) > 0){
                        foreach($enchanting_shard as $index => $item){
                                if(is_int($index)) continue;
                                $max = (int)((int) $enchants_cost["apply_breaking_chance"] / $item[2]);
                                $max = $item[1] >= $max ? $max : $item[1];

                                $form->setSlider($item[0]->getName(), 0, $max, 1, 0);
                        }
                }

                $this->setVar(EnchantingFormData::AVAILABLE_ITEMS_TO_ENCHANT, $items);
                $this->setVar(EnchantingFormData::ENCHANTING_SHARD_USED, $enchanting_shard);
                $this->setVar(EnchantingFormData::AVAILABLE_ITEMS_TO_ENCHANT_INDEXES, $item_indexes);

                //$this->setData([$items, $shard, $itemIndexes, $this->getData()]);

                $form->send($player);
        }

        public function handleResponse(Player $player, $formData){
                if($this->getVar(EnchantingFormData::ITEM_IN_HAND_SLOT_INDEX) !== $player->getInventory()->getHeldItemIndex()){
                        $message = $this->core->getMessage('enchants', 'cannot_apply_air');
                        $player->sendMessage(RandomUtils::colorMessage($message));
                        return;
                }

                $enchantsCost = $this->core->module_loader->enchants->enchantments_cost;
                array_shift($formData);

                $item_selected = array_shift($formData);
                $xp_amount = array_shift($formData);

                $enchanting_shard_chance_increase = $enchantsCost["apply_succeeding_chance"] + ($xp_amount * ((float)$enchantsCost["apply_xp_chance_per_level"]));
                $enchanting_shard_chance_decrease = (float) $enchantsCost["apply_breaking_chance"];

                if($enchanting_shard_chance_increase > 100){
                        $enchanting_shard_chance_increase = 100;
                }

                $enchanting_shard_output = [];

                $enchanting_shard = $this->getVar(EnchantingFormData::ENCHANTING_SHARD_USED);
                foreach($formData as $index => $datum){
                        if(isset($enchanting_shard[$index])){
                                $hash = $enchanting_shard[$index];
                                $data = $enchanting_shard[$hash];

                                $enchanting_shard_output [] = [$data[0], $datum];
                                $enchanting_shard_chance_decrease -= $data[2] * $datum;

                                if($enchanting_shard_chance_decrease < 0){
                                        $enchanting_shard_chance_decrease = 0;
                                }
                        }
                }

                $handler = $this->core->module_loader->form_manager->getHandler($this->core->module_loader->enchants->CONFIRM_APPLY_ID);

                $items = $this->getVar(EnchantingFormData::AVAILABLE_ITEMS_TO_ENCHANT);
                $item_indexes = $this->getVar(EnchantingFormData::AVAILABLE_ITEMS_TO_ENCHANT_INDEXES);

                $handler->setVar(EnchantingFormData::ITEM_IN_HAND, $player->getInventory()->getItemInHand());
                $handler->setVar(EnchantingFormData::SELECTED_ITEM, $items[$item_selected]);
                $handler->setVar(EnchantingFormData::SELECTED_ITEM_INDEX, $item_indexes[$item_selected]);
                $handler->setVar(EnchantingFormData::SELECTED_XP_AMOUNT, $xp_amount);
                $handler->setVar(EnchantingFormData::ENCHANTING_SHARD_INCREASE, $enchanting_shard_chance_increase);
                $handler->setVar(EnchantingFormData::ENCHANTING_SHARD_DECREASE, $enchanting_shard_chance_decrease);
                $handler->setVar(EnchantingFormData::ENCHANTING_SHARD_OUTPUT, $enchanting_shard_output);
                $handler->setVar(EnchantingFormData::ITEM_IN_HAND_SLOT_INDEX, $player->getInventory()->getHeldItemIndex());

                $handler->send($player);
        }
}