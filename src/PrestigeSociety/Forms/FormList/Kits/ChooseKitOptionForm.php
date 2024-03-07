<?php
namespace PrestigeSociety\Forms\FormList\Kits;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\UIForms\SimpleForm;
class ChooseKitOptionForm extends FormHandler{
        public function send(Player $player){
                $form = new SimpleForm($this);
                $kit = $this->getData();

                $items = $this->core->module_loader->kits->getKitItems($kit);
                $items = array_map(function (Item $item){
                        $enchantments = $item->getEnchantments();
                        $enchantments = array_map(function (EnchantmentInstance $enchantment){
                                return RandomUtils::getNameFromTranslatable($enchantment) . " (" . $enchantment->getLevel() .")";
                        }, $enchantments);
                        $output = $item->getName() . " (x" . $item->getCount() .  ")";
                        if(count($enchantments) > 0){
                                $output .= "\n&l&8» &r&7" . implode(", ", $enchantments) . "\n";
                        }
                        return $output;
                }, $items);

                $form->setTitle(RandomUtils::colorMessage("&l&8CHOOSE OPTION FOR " . strtoupper($kit) . " KIT"));
                $content = "===========================\n";
                $content .= "&7Choose view to view the kit items\n";
                $content .= "&7Choose claim to claim the kit items (requires permission)\n\n";

                foreach($items as $item){
                        $content .= "&l&8» &r&7$item\n";
                }

                $content .= "===========================";

                $form->setContent(RandomUtils::colorMessage($content));
                $form->setButton(RandomUtils::colorMessage("&8view"));
                if($player->hasPermission("kit.$kit")){
                        $form->setButton(RandomUtils::colorMessage("&8claim"));
                }

                $form->send($player);
        }

        /**
         * @param Player $player
         * @param        $formData
         *
         * @throws \Exception
         */
        public function handleResponse(Player $player, $formData){
                $kit = $this->getData();
                if($formData === 0){
                        $this->core->module_loader->kits->openViewInventory($player, $kit);
                }else{
                        $this->core->module_loader->kits->openClaimInventory($player, $kit);
                }
        }
}