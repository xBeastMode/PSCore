<?php
namespace PrestigeSociety\Forms\FormList\Shop;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\StringUtils;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\UIForms\CustomForm;
class AddShopForm extends FormHandler{
        public function send(Player $player){
                if($player->getInventory()->getItemInHand()->getId() !== ItemIds::AIR){
                        $ui = new CustomForm($this);
                        $ui->setTitle(RandomUtils::colorMessage("&l&8ADD SHOP"));
                        $ui->setInput(RandomUtils::colorMessage("&7price"), "The price of the item you are selling.");
                        $ui->setSlider(RandomUtils::colorMessage("&7count"), 1, $player->getInventory()->getItemInHand()->getMaxStackSize(), 1, 1);
                        $ui->send($player);
                }else{
                        $player->sendMessage(RandomUtils::colorMessage($this->core->getMessage("shop", "cannot_have_air")));
                }
        }

        public function handleResponse(Player $player, $formData){
                if($formData[0] !== (string)(int)($formData[0]) or $formData[0] === null){
                        $player->sendMessage(RandomUtils::colorMessage($this->core->getMessage("shop", "enter_valid_price")));
                        return;
                }

                $queue = $this->getData();

                $item = $player->getInventory()->getItemInHand();
                $item->setCount($formData[1]);
                $this->core->module_loader->shop->addNewShop($item, StringUtils::stringToInteger($formData[0]), $queue["category"]);
                $player->sendMessage(RandomUtils::colorMessage(str_replace(["@player", "@item", "@price", "@amount"], [$player->getName(), $item->getName(), $formData[0], $formData[1]], $this->core->getMessage("shop", "added_item"))));

        }
}