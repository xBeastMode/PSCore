<?php
namespace PrestigeSociety\Forms\FormList\Management;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;
use pocketmine\utils\Random;
use PrestigeSociety\Core\Utils\StringUtils;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Enchants\Enchants;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\Forms\FormList\Stones\MessageForm;
use PrestigeSociety\InventoryMenu\TransactionData;
use PrestigeSociety\UIForms\CustomForm;
class ConfirmAddSlotForm extends FormHandler{
        public function send(Player $player){
                $form = new CustomForm($this);

                /** @var Item $item */
                $item = $this->getData()[0];
                $enchantmentType = $this->getData()[1];

                $itemInHand = $player->getInventory()->getItemInHand();
                $itemName = $itemInHand->getName();

                if(!$item->equalsExact($itemInHand)){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                            "Please do not switch slots while managing item.", $this->form_id, MessageForm::METADATA_CLOSE
                        ]);
                        return;
                }

                $boostCost = $this->core->module_loader->management->getBoostCost();
                $boostChance = $this->core->module_loader->management->getBoostChance();
                $enchantmentCost = $this->core->module_loader->management->getEnchantmentCost($enchantmentType);

                $availableEnchantments = $this->core->module_loader->management->getCompatibleEnchantments($item, $enchantmentType);
                $availableEnchantments = array_filter($availableEnchantments, function (EnchantmentInstance $instance) use ($item){
                        return !$item->hasEnchantment($instance->getType());
                });

                $slots = array_map(function (EnchantmentInstance $instance){
                        return RandomUtils::getNameFromTranslatable($instance);
                }, $availableEnchantments);

                if(count($slots) <= 0){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                            "No more enchantments of this type can be applied to this item.", $this->form_id, MessageForm::METADATA_CLOSE
                        ]);
                        return;
                }

                $form->setTitle(RandomUtils::colorMessage("&l&8CONFIRM ADD SLOT"));

                $content = "===========================\n";
                $content .= "&7item: &f$itemName\n";
                $content .= "&7enchantment type: &f$enchantmentType\n";
                $content .= "&7enchantment cost: &f$enchantmentCost\n\n";
                $content .= "&eYou may boost your chance of getting a desired enchantment by $boostChance percent for $$boostCost&r\n";
                $content .= "===========================\n";
                $form->setLabel(RandomUtils::colorMessage($content));

                array_unshift($slots, "none");
                $form->setDropdown(RandomUtils::colorMessage("&7boost enchantment"), $slots, 0);

                $data = $this->getData();
                $this->setData([$availableEnchantments, $enchantmentCost, $data]);
                $form->send($player);
        }

        public function handleResponse(Player $player, $formData){
                $data = $this->getData();
                $enchantments = $data[0];
                $cost = $data[1];

                $oldData = $data[2];

                $boostCost = $this->core->module_loader->management->getBoostCost();
                $boostChance = $this->core->module_loader->management->getBoostChance();

                $boosted = $enchantments[$formData[1] - 1] ?? null;
                if($boosted !== null){
                        $cost += $boostCost;
                }

                if($this->core->module_loader->economy->getMoney($player) < $cost){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                            "You do not have enough funds to purchase this.", $this->form_id, MessageForm::METADATA_CLOSE
                        ]);
                        return;
                }

                $success = false;
                $chest_inventory = $this->core->module_loader->inventory_menu->openDoubleChestInventory($player, function (TransactionData $transactionData) use ($player, $data, $cost, $boostChance, $boosted, &$success){
                        $item = $transactionData->source_item;
                        if($item->getNamedTag()->getString("__enchantment__", false) != false){
                                $enchantmentId = $item->getNamedTag()->getString("__enchantment__");
                                $enchantmentLevel = $item->getNamedTag()->getInt("__enchantment_level__");

                                $itemInHand = $player->getInventory()->getItemInHand();

                                $player->getInventory()->clear($player->getInventory()->getHeldItemIndex());
                                $enchantment = new EnchantmentInstance(RandomUtils::smartParseEnchantment($enchantmentId), $enchantmentLevel);

                                $chance = mt_rand(1, 100);

                                if($boosted !== null && $chance <= $boostChance) $enchantment = $boosted;
                                $itemInHand->addEnchantment($enchantment);

                                $player->getInventory()->addItem($itemInHand);
                                $this->core->module_loader->economy->subtractMoney($player, $cost);

                                $name = RandomUtils::getNameFromTranslatable($enchantment);

                                $message = $this->core->getMessage("management", "set_enchantment");
                                $message = str_replace(["@item", "@enchantment", "@level", "@cost"], [$itemInHand->getName(), $name, $enchantment->getLevel(), $cost], $message);
                                $player->sendMessage(RandomUtils::colorMessage($message));

                                RandomUtils::playSound("firework.blast", $player, 1000, 1, true);

                                $this->core->module_loader->inventory_menu->closeInventory($player);
                                $success = true;
                        }
                        return true;
                }, [
                    "height" => 5,
                    "title" => RandomUtils::colorMessage("&l&8PICK A BOOK")
                ]);

                $this->core->module_loader->inventory_menu->setCloseCallback($player, function () use (&$player, &$success, &$oldData){
                        if(!$success && $player->isOnline()){
                                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                                    "Inventory was closed, would you like to try again?", $this->form_id, $oldData
                                ]);
                        }
                });

                shuffle($enchantments);

                $random = new Random();
                for($i = 0; $i < $chest_inventory->getSize(); $i++){
                        if(count($enchantments) <= 0) continue;

                        /** @var EnchantmentInstance $enchantment */
                        $enchantment = array_shift($enchantments);
                        $book = ItemFactory::getInstance()->get(ItemIds::ENCHANTED_BOOK);

                        $book->setCustomName(RandomUtils::colorMessage("&r&e&k" . StringUtils::randomString(16)));
                        $name = RandomUtils::getNameFromTranslatable($enchantment);

                        $book->getNamedTag()->setString("__enchantment__", $name);
                        $book->getNamedTag()->setInt("__enchantment_level__", $enchantment->getLevel());

                        $chest_inventory->setItem($random->nextRange(0, $chest_inventory->getSize() - 1), $book);
                }
        }
}