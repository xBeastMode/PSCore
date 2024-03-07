<?php
namespace PrestigeSociety\Forms\FormList\Blacksmith;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\Forms\FormList\Stones\MessageForm;
use PrestigeSociety\UIForms\CustomForm;
class RenameForm extends FormHandler{
        public function send(Player $player){
                $inHand = $player->getInventory()->getItemInHand();

                if($inHand->getId() === ItemIds::AIR){
                        $message = $this->core->getMessage("item_repair", "cannot_rename_air");
                        $player->sendMessage(RandomUtils::colorMessage($message));
                        return;
                }

                $cost = $player->hasPermission("command.blacksmith") ? 0 : (int) $this->core->module_configurations->repair_prices["rename_price"];

                $form = new CustomForm($this);
                $form->setTitle(RandomUtils::colorMessage("&8&lRENAME ITEM"));
                $content = "===========================\n";
                $content .= "&7rename cost: " . ($player->hasPermission("command.blacksmith") ? "FREE" : $cost) . "&r\n";
                $content .= "===========================\n";
                $form->setLabel(RandomUtils::colorMessage($content));
                $form->setInput(RandomUtils::colorMessage("&7new name"), "enter the desired item name (colors allowed)");
                $form->send($player);

                $this->setData($cost);
        }

        public function handleResponse(Player $player, $formData){
                $cost = $this->getData();

                if(($money = $this->core->module_loader->economy->getMoney($player)) < $cost){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                            RandomUtils::colorMessage("You don't have enough cash to purchase this\nYou need: {$cost}\nYou have: $money."), $this->form_id, MessageForm::METADATA_CLOSE
                        ]);
                        return;
                }

                $name = RandomUtils::colorMessage($formData[1]);

                $inHand = $player->getInventory()->getItemInHand();
                $inHand->setCustomName($name);

                $player->getInventory()->setItemInHand($inHand);
                $this->core->module_loader->economy->subtractMoney($player, $cost);

                $message = $this->core->getMessage("item_repair", "rename_success");
                $message = str_replace("@name", $name, $message);
                $player->sendMessage(RandomUtils::colorMessage($message));
        }
}