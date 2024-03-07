<?php
namespace PrestigeSociety\Forms\FormList\Management;
use pocketmine\block\BlockToolType;
use pocketmine\item\Item;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\Forms\FormList\Stones\MessageForm;
use PrestigeSociety\UIForms\SimpleForm;
class ManageAbilityForm extends FormHandler{
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

                $form->setTitle(RandomUtils::colorMessage("&l&8MANAGE ABILITY OF $itemName"));

                $ability = $this->core->module_loader->management->getItemAbility($itemInHand);

                if($ability !== null){
                        $abilityName = $this->core->module_loader->management->abilityIdToName($ability);
                        $duration = $this->core->module_loader->management->getItemAbilityDuration($itemInHand);

                        $content = "===========================\n";
                        $content .= "&7current ability: &f$abilityName\n";
                        $content .= "&7ability duration: &f$duration\n";
                        $content .= "===========================\n";
                        $form->setContent(RandomUtils::colorMessage($content));
                }else{
                        $content = "===========================\n";
                        $content .= "&7current ability: &fnone\n";
                        $content .= "&7ability duration: &f0\n";
                        $content .= "===========================\n";
                        $form->setContent(RandomUtils::colorMessage($content));
                }

                $toolType = $itemInHand->getBlockToolType();
                if($toolType > BlockToolType::NONE){
                        $form->setButton(RandomUtils::colorMessage("&8set ability"));
                        $form->setButton(RandomUtils::colorMessage("&8remove ability"));
                }else{
                        if($item !== $itemInHand){
                                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                                    "Incompatible item, must be tool or sword.", $this->form_id, MessageForm::METADATA_CLOSE
                                ]);
                                return;
                        }
                }

                $this->setData($item);
                $form->send($player);
        }

        public function handleResponse(Player $player, $formData){
                $forms = [
                    $this->core->module_loader->management->SET_ABILITY_ID,
                    $this->core->module_loader->management->REMOVE_ABILITY_ID
                ];
                $this->core->module_loader->form_manager->sendForm($forms[$formData], $player, $this->getData());
        }
}