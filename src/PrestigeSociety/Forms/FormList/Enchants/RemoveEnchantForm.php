<?php
namespace PrestigeSociety\Forms\FormList\Enchants;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Enchants\Enchants;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\Forms\FormList\Stones\MessageForm;
use PrestigeSociety\UIForms\CustomForm;
class RemoveEnchantForm extends FormHandler{
        /**
         * @param Player $player
         */
        public function send(Player $player){
                $item_in_hand = $player->getInventory()->getItemInHand();
                if($item_in_hand->getId() !== ItemIds::AIR){
                        $enchantments = $item_in_hand->getEnchantments();

                        if(count($enchantments) <= 0){
                                $message = $this->core->getMessage('enchants', 'no_enchants_found');
                                $player->sendMessage(RandomUtils::colorMessage($message));
                                return;
                        }

                        $enchantment_names = [];
                        foreach($enchantments as $enchant){
                                $enchantment_names[] = Enchants::VANILLA_CONVERTER[$enchant->getType()->getName()] ?? $enchant->getType()->getName();
                        }

                        $enchants_cost = $this->core->module_loader->enchants->enchantments_cost;

                        /** @var Item[][]|int[][] $enchanting_shard */
                        $enchanting_shard = [];
                        $indexes = 0;

                        $content = "===========================\n";
                        $content .= "&7Welcome to the enchantment abstract!\n\n";
                        $content .= "&7The abstract is used to remove enchantments from your items.\n";
                        $content .= "&7The abstract has {$enchants_cost["remove_succeeding_chance"]} percent chance of returning the enchantment in a book.\n";
                        $content .= "&7Use the following items to change these chances:\n\n";

                        foreach($enchants_cost["remove"] as $merge){
                                $content .= "&7{$merge["name"]}&r&7 increases {$merge["worth"]}% chance of returning enchantment\n\n";
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
                        $content .= "&7Removal cost: {$enchants_cost["remove_cost"]}\n";

                        $content .= "===========================\n";

                        $ui = new CustomForm($this);
                        $ui->setTitle(RandomUtils::colorMessage("&c&lENCHANTMENT ABSTRACT"));
                        $ui->setLabel(RandomUtils::colorMessage($content));
                        $ui->setDropdown(RandomUtils::colorMessage("&7choose enchantment"), $enchantment_names, 0);

                        if(count($enchanting_shard) > 0){
                                foreach($enchanting_shard as $index => $item){
                                        if(is_int($index)) continue;
                                        $max = (int)((100 - (int)$enchants_cost["remove_succeeding_chance"]) / $item[2]);
                                        $max = $item[1] >= $max ? $max : $item[1];

                                        $ui->setSlider($item[0]->getName(), 0, $max, 1, 0);
                                }
                        }

                        $ui->send($player);

                        $this->setVar(EnchantingFormData::ITEM_IN_HAND, $item_in_hand);
                        $this->setVar(EnchantingFormData::OUTPUT_ENCHANTMENTS, $enchantments);
                        $this->setVar(EnchantingFormData::ENCHANTING_SHARD_USED, $enchanting_shard);
                        $this->setVar(EnchantingFormData::ITEM_IN_HAND_SLOT_INDEX, $player->getInventory()->getHeldItemIndex());

                        //$this->setData([$inHand, $enchants, $enchanting_shard, $this->getData()]);
                }else{
                        $message = $this->core->getMessage('enchants', 'cannot_unenchant_air');
                        $player->sendMessage(RandomUtils::colorMessage($message));
                }
        }

        /**
         * @param Player $player
         * @param        $formData
         */
        public function handleResponse(Player $player, $formData){
                if($this->getVar(EnchantingFormData::ITEM_IN_HAND_SLOT_INDEX) !== $player->getInventory()->getHeldItemIndex()){
                        $message = $this->core->getMessage('enchants', 'cannot_apply_air');
                        $player->sendMessage(RandomUtils::colorMessage($message));
                        return;
                }

                /** @var Item $item */
                $item = $this->getVar(EnchantingFormData::ITEM_IN_HAND);
                $enchantments = $this->getVar(EnchantingFormData::OUTPUT_ENCHANTMENTS);

                /** @var EnchantmentInstance $enchant */
                $enchant = $enchantments[$formData[1]];

                array_shift($formData);
                array_shift($formData);

                $enchants_cost = $this->core->module_loader->enchants->enchantments_cost;
                $recover_chance = (float) $enchants_cost["remove_succeeding_chance"];

                $cost = $enchants_cost["remove_cost"];

                if(($money = $this->core->module_loader->economy->getMoney($player)) < $cost){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                            RandomUtils::colorMessage("You don't have enough money to purchase this\nYou need: $cost\nYou have: $money."), $this->form_id, MessageForm::METADATA_CLOSE
                        ]);
                        return;
                }

                /** @var Item[][]|int[][] $output */
                $output = [];

                $enchanting_shard = $this->getVar(EnchantingFormData::ENCHANTING_SHARD_USED);
                foreach($formData as $index => $datum){
                        if(isset($enchanting_shard[$index])){
                                $hash = $enchanting_shard[$index];
                                $data = $enchanting_shard[$hash];

                                $output []= [$data[0], $datum];
                                $recover_chance += $data[2] * $datum;

                                if($recover_chance > 100){
                                        $recover_chance = 100;
                                }
                        }
                }

                foreach($output as $datum){
                        $datum[0]->setCount($datum[1]);
                        $player->getInventory()->removeItem($datum[0]);
                }

                $this->core->module_loader->economy->subtractMoney($player, $cost);
                $item->removeEnchantment($enchant->getType()->getId());

                if(count($item->getEnchantments()) <= 0) $item->getNamedTag()->removeTag("ench");
                $player->getInventory()->setItemInHand($item);

                RandomUtils::playSound("random.anvil_use", $player);

                $message = $this->core->getMessage('enchants', 'removed_enchant');
                $message = str_replace(["@name", "@level"], [Enchants::VANILLA_CONVERTER[$enchant->getType()->getName()] ?? $enchant->getType()->getName(), $enchant->getLevel()], $message);
                $player->sendMessage(RandomUtils::colorMessage($message));

                if(RandomUtils::randomFloat(0, 100) <= $recover_chance){
                        $book = ItemFactory::getInstance()->get(ItemIds::ENCHANTED_BOOK);
                        $lore []= RandomUtils::colorMessage("&r&7go to &6ENCHANTER &7to use");

                        $book->addEnchantment($enchant);

                        $book->setLore($lore);
                        $book->setCustomName(RandomUtils::colorMessage("&r&a&lENCHANTMENT RECOVERY"));
                        $player->getInventory()->addItem($book);

                        $player->sendTip(RandomUtils::colorMessage("&a&lENCHANTMENT RECOVERED"));
                }
        }
}