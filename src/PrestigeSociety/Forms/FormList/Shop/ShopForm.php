<?php
namespace PrestigeSociety\Forms\FormList\Shop;
use pocketmine\item\Bed;
use pocketmine\item\ItemFactory;
use pocketmine\item\Potion;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\UIForms\SimpleForm;
class ShopForm extends FormHandler{
        public function send(Player $player){
                $queue = $this->getData();

                $id = $queue[0];
                $category = $queue[1];
                $amount = $queue[2];

                $shops = $this->core->module_loader->shop->getShop($category, $id);

                /** @var Potion $item */
                $item = ItemFactory::getInstance()->get($shops["itemId"], $shops["itemMeta"]);
                $name = $item instanceof Potion ? RandomUtils::getNameFromTranslatable($item) : $shops["item"];

                if($item instanceof Bed){
                        $name = $item->getColor()->getDisplayName() . " " . $name;
                }

                if(count($shops) > 0){
                        $price = round(($shops["price"] / $shops["amount"]) * $amount);

                        $ui = new SimpleForm($this);
                        $ui->setTitle(RandomUtils::colorMessage("&8&lDO YOU WANT TO BUY THIS ITEM?"));
                        $content = "";
                        $content .= "&f===========================\n";
                        $content .= "&7Item: &f" . $name . "\n";
                        $content .= "&7Item ID: &f" . $shops["itemId"] . "\n";
                        $content .= "&7Item Meta: &f" . $shops["itemMeta"] . "\n";
                        $content .= "&7Amount: &f" . $amount . "\n";
                        $content .= "&7Price: &f" . $price . "\n";
                        $content .= "&f===========================\n";
                        $ui->setContent(RandomUtils::colorMessage($content));
                        $ui->setButton(RandomUtils::colorMessage("&8yes"));
                        $ui->setButton(RandomUtils::colorMessage("&8no"));
                        $ui->send($player);
                }
        }

        public function handleResponse(Player $player, $formData){
                if($formData === 0){
                        $queue = $this->getData();


                        $id = $queue[0];
                        $category = $queue[1];
                        $amount = $queue[2];

                        $shop = $this->core->module_loader->shop->getShop($category, $id);

                        if(count($shop) > 0){
                                $price = round(($shop["price"] / $shop["amount"]) * $amount);

                                /** @var Potion $item */
                                $item = ItemFactory::getInstance()->get($shop["itemId"], $shop["itemMeta"]);
                                $name = $item instanceof Potion ? RandomUtils::getNameFromTranslatable($item) : $shop["item"];

                                if($item instanceof Bed){
                                        $name = $item->getColor()->getDisplayName() . " " . $name;
                                }

                                $item->setCount($amount);
                                if($name !== $shop["item"]){
                                        $item->setCustomName(RandomUtils::colorMessage("&r&f$name"));
                                }

                                if($this->core->module_loader->economy->getMoney($player) < $price){
                                        $player->sendMessage(RandomUtils::colorMessage($this->core->getMessage("shop", "non_sufficient_funds")));
                                }else{
                                        if(!$player->getInventory()->canAddItem($item)){
                                                $player->sendMessage(RandomUtils::colorMessage($this->core->getMessage("shop", "inventory_full")));
                                                return;
                                        }
                                        $player->getInventory()->addItem($item);
                                        $this->core->module_loader->economy->subtractMoney($player, $price);
                                        $player->sendMessage(RandomUtils::colorMessage(str_replace(["@player", "@item", "@price", "@amount"], [$player->getName(), $name, $price, $amount], $this->core->getMessage("shop", "bought_item"))));
                                }
                        }else{
                                $player->sendMessage(RandomUtils::colorMessage($this->core->getMessage("shop", "invalid_item")));
                        }
                }
        }
}