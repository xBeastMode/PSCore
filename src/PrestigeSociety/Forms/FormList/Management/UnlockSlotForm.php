<?php
namespace PrestigeSociety\Forms\FormList\Management;
use pocketmine\item\Item;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\Forms\FormList\Stones\MessageForm;
use PrestigeSociety\UIForms\SimpleForm;
class UnlockSlotForm extends FormHandler{
        public function send(Player $player){
                $form = new SimpleForm($this);

                /** @var Item $item */
                $item = $this->getData();
                $itemInHand = $player->getInventory()->getItemInHand();
                $itemName = $itemInHand->getName();

                if(!$item->equalsExact($itemInHand)){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                            "Please do not switch slots while managing item.", $this->form_id, MessageForm::METADATA_CLOSE
                        ]);
                        return;
                }

                $form->setTitle(RandomUtils::colorMessage("&l&8UNLOCK SLOT"));

                $maxSlots = $this->core->module_loader->management->getItemMaxSlots($item);
                $maxAbsoluteSlots = $this->core->module_loader->management->getMaxSlots();
                $slotsUsed = count($itemInHand->getEnchantments());

                if($maxSlots >= $maxAbsoluteSlots){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                            "Maximum absolute slots unlocked for this item", $this->form_id, MessageForm::METADATA_CLOSE
                        ]);
                        return;
                }

                $unlockCost = $this->core->module_loader->management->getUnlockCost();
                $slotsAfterUnlock = $maxSlots + 1;

                $content = "===========================\n";
                $content .= "&7item: &f$itemName\n";
                $content .= "&7unlock cost: &f$unlockCost\n";
                $content .= "&7current max slots: &f$maxSlots\n";
                $content .= "&7max slots after unlock: &f{$slotsAfterUnlock}\n";
                $content .= "&7current slots used: &f$slotsUsed\n";
                $content .= "===========================\n";
                $form->setContent(RandomUtils::colorMessage($content));

                $form->setButton(RandomUtils::colorMessage("&8yes"));
                $form->setButton(RandomUtils::colorMessage("&8no"));

                $this->setData($itemInHand);
                $form->send($player);
        }

        public function handleResponse(Player $player, $formData){
                if($formData === 0){
                        $unlockCost = $this->core->module_loader->management->getUnlockCost();
                        if($this->core->module_loader->economy->getMoney($player) < $unlockCost){
                                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                                    "You do not have enough funds to purchase this.", $this->form_id, MessageForm::METADATA_CLOSE
                                ]);
                                return;
                        }

                        $this->core->module_loader->economy->subtractMoney($player, $unlockCost);

                        $itemInHand = $player->getInventory()->getItemInHand();
                        $itemResult = $this->core->module_loader->management->addItemMaxSlots($itemInHand);

                        $player->getInventory()->removeItem($player->getInventory()->getItemInHand());
                        $player->getInventory()->addItem($itemResult);

                        $message = $this->core->getMessage("management", "unlock_slot");
                        $message = str_replace(["@item", "@cost"], [$itemInHand->getName(), $unlockCost], $message);
                        $player->sendMessage(RandomUtils::colorMessage($message));
                }
        }
}