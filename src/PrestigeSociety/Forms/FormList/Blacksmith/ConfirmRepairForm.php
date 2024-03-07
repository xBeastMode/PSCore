<?php
namespace PrestigeSociety\Forms\FormList\Blacksmith;
use pocketmine\item\Durable;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\Forms\FormList\Stones\MessageForm;
use PrestigeSociety\UIForms\SimpleForm;
class ConfirmRepairForm extends FormHandler{
        public function send(Player $player){
                $repair = $this->getData()[0];
                $cost = $this->getData()[1];
                $item = $player->getInventory()->getItemInHand();

                if(!$item instanceof Durable){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                            "This item is not allowed.", $this->form_id, MessageForm::METADATA_CLOSE
                        ]);
                        return;
                }

                $damage = $item->getDamage();

                $cost = $player->hasPermission("command.blacksmith") ? 0 : $cost * $repair;

                $ui = new SimpleForm($this);
                $ui->setTitle(RandomUtils::colorMessage("&8&lCONFIRM REPAIR"));
                $ui->setContent(
                    "§7damage: §f$damage\n" .
                    "§7item: §f{$item->getName()}\n" .
                    "§7damage repair: §f$repair\n" .
                    "§7repair cost: §f" . ($player->hasPermission("command.blacksmith") ? "FREE" : $cost)
                );

                $ui->setButton("§8yes");
                $ui->setButton("§8no");
                $ui->send($player);

                $this->setData([$repair, $cost]);
        }

        public function handleResponse(Player $player, $formData){
                $repair = $this->getData()[0];
                $cost = $this->getData()[1];

                if($formData === 0){
                        if(($this->core->module_loader->economy->getMoney($player) <= $cost) && !$player->hasPermission("command.blacksmith")){
                                RandomUtils::playSound("mob.villager.no", $player);

                                $player->sendMessage(RandomUtils::colorMessage($this->core->getMessage("item_repair", "not_enough_funds")));
                                return;
                        }

                        if(!$player->hasPermission("command.blacksmith")){
                                $this->core->module_loader->economy->subtractMoney($player, $cost);
                        }

                        $item = $player->getInventory()->getItemInHand();
                        $item->setDamage($item->getDamage() - $repair);

                        $player->getInventory()->setItemInHand($item);

                        RandomUtils::playSound("random.anvil_use", $player);

                        $player->sendMessage(RandomUtils::colorMessage(
                            str_replace(["@item_name", "@cost"], [$item->getName(), $cost],
                                $this->core->getMessage("item_repair", $player->hasPermission("command.blacksmith") ? "repaired_item_vip" : "repaired_item"))));
                }
        }
}