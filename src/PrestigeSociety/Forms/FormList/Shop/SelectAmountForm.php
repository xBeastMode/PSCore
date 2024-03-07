<?php
namespace PrestigeSociety\Forms\FormList\Shop;
use pocketmine\data\bedrock\LegacyItemIdToStringIdMap;
use pocketmine\item\Bed;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\Potion;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\UIForms\CustomForm;
class SelectAmountForm extends FormHandler{
        public function send(Player $player){
                $queue = $this->getData();

                $id = $queue[0];
                $category = $queue[1];

                $shops = $this->core->module_loader->shop->getShop($category, $id);
                /** @var Potion $item */
                $item = ItemFactory::getInstance()->get($shops["itemId"], $shops["itemMeta"]);
                $name = $item instanceof Potion ? RandomUtils::getNameFromTranslatable($item) : $shops["item"];

                if($item instanceof Bed){
                        $name = $item->getColor()->getDisplayName() . " " . $name;
                }

                if(count($shops) > 0){
                        $form = new CustomForm($this);
                        $form->setTitle(RandomUtils::colorMessage("&8&lSELECT AMOUNT"));

                        $content = "&f===========================\n";
                        $content .= "&7Item: &f" . $name . "\n";
                        $content .= "&f===========================\n";
                        $form->setLabel(RandomUtils::colorMessage($content));

                        $form->setInput(RandomUtils::colorMessage("&7amount"), '', $item->getMaxStackSize());
                        $form->send($player);
                }
        }

        public function handleResponse(Player $player, $formData){
                if(!is_numeric($formData[1])  || ((int) $formData[1] <= 0)){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                            RandomUtils::colorMessage("{$formData[1]} is not a valid number."), $this->form_id, $this->getData()
                        ]);
                        return;
                }

                $data = $this->getData();
                $data[] = (int) $formData[1];
                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->shop->SHOP_ID, $player, $data);
        }
}