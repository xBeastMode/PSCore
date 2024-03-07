<?php
namespace PrestigeSociety\Forms\FormList\Blacksmith;
use pocketmine\item\Durable;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\Forms\FormList\Stones\MessageForm;
use PrestigeSociety\UIForms\CustomForm;
class ChooseDamageRepairForm extends FormHandler{
        public function send(Player $player){
                $items = $this->core->module_configurations->repair_prices;
                $item = $player->getInventory()->getItemInHand();

                if(!$item instanceof Durable){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                            "This item is not allowed.", $this->form_id, MessageForm::METADATA_CLOSE
                        ]);
                        return;
                }

                $itemId = $item->getId();
                $damage = $item->getDamage();

                if($damage > 0){
                        if(isset($items[$itemId])){
                                $cost = $items[$itemId];

                                $ui = new CustomForm($this);
                                $ui->setTitle(RandomUtils::colorMessage("&8&lCHOOSE DAMAGE REPAIR"));
                                $ui->setLabel(
                                    "§7item: §f" . $item->getName()  . "\n" .
                                    "§7damage: §f" . $damage);
                                $ui->setSlider("§7choose damage repair§f", 1, $damage, 1, 1);
                                $ui->send($player);

                                $this->setData([$this->getData(), $cost]);
                        }else{
                                $player->sendMessage(RandomUtils::colorMessage($this->core->getMessage("item_repair", "cannot_repair_item")));
                        }
                }else{
                        $player->sendMessage(RandomUtils::colorMessage($this->core->getMessage("item_repair", "no_damage")));
                }
        }

        public function handleResponse(Player $player, $formData){
                $this->core->module_loader->form_manager->sendForm($this->getData()[0], $player, [$formData[1], $this->getData()[1]]);
        }
}