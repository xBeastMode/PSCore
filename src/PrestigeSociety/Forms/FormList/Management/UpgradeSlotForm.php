<?php
namespace PrestigeSociety\Forms\FormList\Management;
use DaPigGuy\PiggyCustomEnchants\CustomEnchantManager;
use DaPigGuy\PiggyCustomEnchants\enchants\CustomEnchant;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\Forms\FormList\Stones\MessageForm;
use PrestigeSociety\Management\Management;
use PrestigeSociety\UIForms\SimpleForm;

class UpgradeSlotForm extends FormHandler{
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

                $custom = $enchantment->getType() instanceof CustomEnchant ? Management::ENCHANTMENTS_CUSTOM : Management::ENCHANTMENTS_VANILLA;
                $cost = $this->core->module_loader->management->getEnchantmentCost($custom);

                $form->setTitle(RandomUtils::colorMessage("&l&8UPGRADE SLOT"));

                if($enchantment->getLevel() >= $enchantment->getType()->getMaxLevel()){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                            "Maximum level reached for this enchantment", $this->form_id, MessageForm::METADATA_CLOSE
                        ]);
                        return;
                }

                $levelAfterUpgrade = $enchantment->getLevel() + 1;
                $cost = $cost * $levelAfterUpgrade;

                $name = RandomUtils::getNameFromTranslatable($enchantment);

                $content = "===========================\n";
                $content .= "&7item: &f$itemName\n";
                $content .= "&7enchantment: &f{$name}\n";
                $content .= "&7current level: &f{$enchantment->getLevel()}\n";
                $content .= "&7next level: &f{$levelAfterUpgrade}\n";
                $content .= "&7upgrade cost: &f$cost\n";
                $content .= "===========================\n";
                $form->setContent(RandomUtils::colorMessage($content));

                $form->setButton(RandomUtils::colorMessage("&8yes"));
                $form->setButton(RandomUtils::colorMessage("&8no"));

                $form->send($player);
        }

        public function handleResponse(Player $player, $formData){
                if($formData === 0){
                        $itemInHand = $player->getInventory()->getItemInHand();

                        $enchantmentIndex = $this->getData()[1];
                        $enchantment = array_values($itemInHand->getEnchantments())[$enchantmentIndex];

                        $custom = $enchantment->getType() instanceof CustomEnchant ? Management::ENCHANTMENTS_CUSTOM : Management::ENCHANTMENTS_VANILLA;
                        $levelAfterUpgrade = $enchantment->getLevel() + 1;

                        $cost = $this->core->module_loader->management->getEnchantmentCost($custom);
                        $cost = $cost * $levelAfterUpgrade;

                        if($this->core->module_loader->economy->getMoney($player) < $cost){
                                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                                    "You do not have enough funds to purchase this.", $this->form_id, MessageForm::METADATA_CLOSE
                                ]);
                                return;
                        }

                        $this->core->module_loader->economy->subtractMoney($player, $cost);
                        $player->getInventory()->removeItem($player->getInventory()->getItemInHand());

                        $itemInHand->addEnchantment(new EnchantmentInstance($enchantment->getType(), $levelAfterUpgrade));

                        $player->getInventory()->addItem($itemInHand);

                        $message = $this->core->getMessage("management", "upgrade_slot");
                        $message = str_replace(["@item", "@enchantment", "@cost", "@last", "@level"], [$itemInHand->getName(), RandomUtils::getNameFromTranslatable($enchantment), $cost, $enchantment->getLevel(), $levelAfterUpgrade], $message);
                        $player->sendMessage(RandomUtils::colorMessage($message));
                }
        }
}