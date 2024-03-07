<?php
namespace PrestigeSociety\Forms\FormList\Shop;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\StringUtils;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\UIForms\SimpleForm;

class SelectCategoryForm extends FormHandler{
        protected array $images = [
            "Armor"         => "armor",
            "Beds"          => "bed",
            "Carpet"        => "blocks",
            "Common Blocks" => "blocks",
            "Concrete"      => "blocks",
            "Decoration"    => "decoration",
            "Doors/Gates"   => "blocks",
            "Farming"       => "farming",
            "Fencing"       => "blocks",
            "Food"          => "food",
            "Gadgets"       => "misc",
            "Glass"         => "blocks",
            "Lighting"      => "lighting",
            "Minerals"      => "misc",
            "Miscellaneous" => "misc",
            "Potions"       => "potions",
            "Slabs"         => "blocks",
            "Terracotta"    => "blocks",
            "Tools"         => "tools",
            "Weapons"       => "weapons",
            "Wood"          => "blocks",
            "Wool"          => "blocks",
            ];

        protected function categoryImage(string $category): array|string{
                return StringUtils::_("https://raw.githubusercontent.com/xBeastMode/psicons2/master/%1.png", [$category]);
        }

        protected function orderCategories(SimpleForm $ui){
                foreach($this->images as $cat => $img){
                        $ui->setButton(RandomUtils::colorMessage(
                            "&7- &8" . $cat . " &7-"
                        ), $this->categoryImage($img));
                }
        }

        public function send(Player $player){
                $ui = new SimpleForm($this);
                $ui->setTitle(RandomUtils::colorMessage("&8&lSELECT CATEGORY"));
                $this->orderCategories($ui);
                $ui->send($player);
        }

        public function handleResponse(Player $player, $formData){
                $queue = $this->getData();

                $queue["category"] = $formData;

                switch($queue["action"]){
                        case 0:
                                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->shop->ADD_SHOP_ID, $player, $queue);
                                break;
                        case 1:
                                $queue["idAction"] = 0;
                                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->shop->SELECT_ITEM_ID, $player, $queue);
                                break;
                        case 2:
                                $queue["idAction"] = 1;
                                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->shop->SELECT_ITEM_ID, $player, $queue);
                                break;
                }
        }
}