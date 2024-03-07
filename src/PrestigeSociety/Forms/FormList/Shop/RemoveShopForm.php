<?php
namespace PrestigeSociety\Forms\FormList\Shop;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\UIForms\SimpleForm;
class RemoveShopForm extends FormHandler{
        public function send(Player $player){
                $id = $this->getData()[0];
                $category = $this->getData()[1];

                $shops = $this->core->module_loader->shop->getShop($category, $id);
                if(count($shops) > 0){
                        $ui = new SimpleForm($this);
                        $ui->setTitle(RandomUtils::colorMessage("&l&8DO YOU WANT TO REMOVE THIS SHOP?"));
                        //$ui->setButton("", "http://permat.comli.com/items/{$shops["itemId"]}-{$shops["itemMeta"]}.png");
                        $content = "";
                        $content .= "&f===========================\n";
                        $content .= "&r&7Item: &f" . $shops["item"] . "\n";
                        $content .= "&r&7Item ID: &f" . $shops["itemId"] . "\n";
                        $content .= "&r&7Item Meta: &f" . $shops["itemMeta"] . "\n";
                        $content .= "&r&7Amount: &f" . $shops["amount"] . "\n";
                        $content .= "&r&7Price: &f" . $shops["price"] . "\n";
                        $content .= "&f===========================\n";
                        $ui->setContent(RandomUtils::colorMessage($content));
                        $ui->setButton(RandomUtils::colorMessage("&8yes"));
                        $ui->setButton(RandomUtils::colorMessage("&8no"));
                        $ui->send($player);
                }
        }

        public function handleResponse(Player $player, $formData){
                if($formData === 0){
                        $id = $this->getData()[0];
                        $category = $this->getData()[1];

                        $shop = $this->core->module_loader->shop->getShop($category, $id);
                        $this->core->module_loader->shop->removeShop($category, $id);

                        $player->sendMessage(RandomUtils::colorMessage(str_replace(["@player", "@item", "@price", "@amount"], [$player->getName(), $shop["item"], $shop["price"], $shop["amount"]], $this->core->getMessage("shop", "removed_item"))));
                }

        }
}