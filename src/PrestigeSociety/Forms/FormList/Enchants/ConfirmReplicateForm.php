<?php
namespace PrestigeSociety\Forms\FormList\Enchants;
use pocketmine\item\Item;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\Forms\FormList\Stones\MessageForm;
use PrestigeSociety\UIForms\SimpleForm;
class ConfirmReplicateForm extends FormHandler{

        public function send(Player $player){
                $costs = (int) $this->core->module_loader->enchants->enchantments_cost["replicate_cost"];

                $enchanting_shard_chance_increase = $this->getVar(EnchantingFormData::ENCHANTING_SHARD_INCREASE);
                $enchanting_shard_chance_decrease = $this->getVar(EnchantingFormData::ENCHANTING_SHARD_DECREASE);

                $form = new SimpleForm($this);
                $form->setTitle(RandomUtils::colorMessage("&c&lCONFIRM FACSIMILE"));
                $content = "===========================\n";
                $content .= "&7total cost: &f$costs\n";
                $content .= "&7success chance: &f$enchanting_shard_chance_increase percent\n";
                $content .= "&7breaking chance: &f$enchanting_shard_chance_decrease percent\n";
                $content .= "===========================\n";
                $form->setContent(RandomUtils::colorMessage($content));
                $form->setButton(RandomUtils::colorMessage("&8create facsimile"));
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

                        $costs = (int) $this->core->module_loader->enchants->enchantments_cost["replicate_cost"];

                        /** @var Item $item_in_hand */
                        $item_in_hand = $this->getVar(EnchantingFormData::ITEM_IN_HAND);
                        $item_in_hand->clearCustomBlockData();

                        $enchanting_shard_chance_increase = $this->getVar(EnchantingFormData::ENCHANTING_SHARD_INCREASE);
                        $enchanting_shard_chance_decrease = $this->getVar(EnchantingFormData::ENCHANTING_SHARD_DECREASE);


                        if(($money = $this->core->module_loader->economy->getMoney($player)) < $costs){
                                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                                    RandomUtils::colorMessage("You don't have enough money to purchase this\nYou need: $costs\nYou have: $money."), $this->form_id, MessageForm::METADATA_CLOSE
                                ]);
                                return;
                        }

                        /** @var Item[][]|int[][] $enchanting_shard_output */
                        $enchanting_shard_output = $this->getVar(EnchantingFormData::ENCHANTING_SHARD_OUTPUT);

                        foreach($enchanting_shard_output as $datum){
                                $datum[0]->setCount($datum[1]);
                                $player->getInventory()->removeItem($datum[0]);
                        }

                        $player->getXpManager()->setXpLevel($player->getXpManager()->getXpLevel() - $this->getData()[5]);
                        $this->core->module_loader->economy->subtractMoney($player, $costs);

                        if(RandomUtils::randomFloat(0, 100) <= $enchanting_shard_chance_decrease){
                                RandomUtils::playSound("random.break", $player);

                                $player->getInventory()->removeItem($item_in_hand);
                                $player->sendTip(RandomUtils::colorMessage("&4&lBOOK WAS BROKEN"));

                                $message = $this->core->getMessage('enchants', 'replicate_fail');
                                $player->sendMessage(RandomUtils::colorMessage($message));
                        }else{
                                if(RandomUtils::randomFloat(0, 100) <= $enchanting_shard_chance_increase){
                                        $player->getInventory()->addItem($item_in_hand);

                                        RandomUtils::playSound("random.anvil_use", $player);

                                        $message = $this->core->getMessage('enchants', 'replicate_success');
                                        $player->sendMessage(RandomUtils::colorMessage($message));
                                }else{
                                        RandomUtils::playSound("random.break", $player);

                                        $message = $this->core->getMessage('enchants', 'replicate_fail');
                                        $player->sendMessage(RandomUtils::colorMessage($message));
                                }
                        }
                }else{
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->enchants->ENCHANTS_ID, $player);
                }
        }
}