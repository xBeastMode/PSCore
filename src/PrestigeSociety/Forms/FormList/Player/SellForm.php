<?php
namespace PrestigeSociety\Forms\FormList\Player;
use pocketmine\item\Item;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\Forms\FormList\Stones\MessageForm;
use PrestigeSociety\UIForms\CustomForm;
class SellForm extends FormHandler{
        public function send(Player $player){
                $ui = new CustomForm($this);
                $ui->setTitle(RandomUtils::colorMessage("&8&lSELL YOUR ITEMS"));
                $ui->setDropdown(RandomUtils::colorMessage("&7sell type"), ["hand items", "all items"], 0);
                $ui->send($player);
        }

        public function handleResponse(Player $player, $formData){
                if($formData[0] === 0){
                        $this->sellHand($player);
                }else{
                        $this->sellAll($player);
                }
        }

        private function sellHand(Player $player){
                $items = [];
                $item = $player->getInventory()->getItemInHand();

                $prices = $this->getData();

                foreach($player->getInventory()->getContents() as $content){
                        if($content->equals($item, true, false) && isset($prices[$content->getId() . ":" . $content->getMeta()])){
                                $items []= $content;
                        }
                }

                $this->sell($player, $items);
        }

        private function sellAll(Player $player){
                $items = [];
                $prices = $this->getData();

                foreach($player->getInventory()->getContents() as $content){
                        if(isset($prices[$content->getId() . ":" . $content->getMeta()])){
                                $items []= $content;
                        }
                }

                $this->sell($player, $items);
        }

        /**
         * @param Player $player
         * @param Item[] $items
         */
        private function sell(Player $player, array $items){
                if(count($items) <= 0){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MESSAGE_ID, $player, [
                            RandomUtils::colorMessage("You have no items that can be sold."), $this->form_id, MessageForm::METADATA_CLOSE
                        ]);
                        return;
                }

                $prices = $this->getData();
                $finalPrice = 0;
                $items_r = [];
                $items_c = [];

                foreach($items as $item){
                        $index = $item->getId() . ":" . $item->getMeta();

                        $price = $prices[$index];
                        $price = intval($price) * $item->getCount();

                        if(isset($items_c[$index])){
                                $items_c[$index] += $item->getCount();
                                $items_r[$index] = $item->getName() . " (x{$items_c[$index]})";
                        }else{
                                $items_c[$index] = $item->getCount();
                                $items_r[$index] = $item->getName() . " (x{$items_c[$index]})";
                        }

                        $finalPrice += $price;
                        $player->getInventory()->removeItem($item);
                }

                $this->core->module_loader->economy->withdraw($player, $finalPrice);

                $message = $this->core->getMessage("sell_all", "sold_all");
                $message = str_replace(["@items", "@price"], [implode(", ", $items_r), $finalPrice], $message);
                $player->sendMessage(RandomUtils::colorMessage($message));
        }
}