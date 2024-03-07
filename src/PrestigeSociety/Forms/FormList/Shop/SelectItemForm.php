<?php
namespace PrestigeSociety\Forms\FormList\Shop;
use pocketmine\item\Bed;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\Potion;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\UIForms\SimpleForm;
class SelectItemForm extends FormHandler{
        const METADATA_NO_BACK = "meta:no_back";

        public function send(Player $player){
                $queue = $this->getData();
                $category = $queue["category"];
                $shops = $this->core->module_loader->shop->getShopItems($category);
                if(count($shops) > 0){
                        $ui = new SimpleForm($this);
                        $ui->setTitle(RandomUtils::colorMessage("&8&l" . strtoupper($this->core->module_loader->shop->categoryToString($category))));
                        $ui->setContent("");

                        $selecting = [];

                        if(in_array(self::METADATA_NO_BACK, $queue)){
                                $i = 0;
                        }else{
                                $i = 1;
                                $ui->setButton(RandomUtils::colorMessage("&l&8« &r&8back"));
                        }

                        foreach($shops as $shop){
                                $selecting[$i++] = $shop["id"];
                                /** @var Potion $item */
                                $item = ItemFactory::getInstance()->get($shop["itemId"], $shop["itemMeta"]);
                                $name = $item instanceof Potion ? RandomUtils::getNameFromTranslatable($item) : $shop["item"];

                                if($item instanceof Bed){
                                        $name = $item->getColor()->getDisplayName() . " " . $name;
                                }

                                $ui->setButton(RandomUtils::colorMessage(
                                    "&7- &8{$name} &7-\n" .
                                    "&2$" . round($shop["price"] / $shop["amount"]) . " &8each"
                                ), "https://raw.githubusercontent.com/xBeastMode/minecraft-png-images-id-meta-api/master/list/{$shop["itemId"]}-{$shop["itemMeta"]}.png");
                        }
                        $ui->send($player);

                        $this->setData([$queue, $selecting]);
                }else{
                        $player->sendMessage(RandomUtils::colorMessage($this->core->getMessage("shop", "no_items")));
                }
        }

        public function handleResponse(Player $player, $formData){
                $queue = $this->getData()[0];
                $selecting = $this->getData()[1];

                if($formData === 0 && !in_array(self::METADATA_NO_BACK, $queue)){
                        $this->core->module_loader->form_manager->sendForm($this->core->module_loader->shop->SELECT_CATEGORY_ID, $player, $this->getData()[0]);
                        return;
                }

                $id = $selecting[$formData];
                $category = $queue["category"];

                switch($queue["idAction"]){
                        case 0:
                                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->shop->REMOVE_SHOP_ID, $player, [$id, $category]);
                                break;
                        case 1:
                                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->shop->SELECT_AMOUNT_ID, $player, [$id, $category]);
                                break;
                }
        }
}