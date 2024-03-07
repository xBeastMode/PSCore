<?php
namespace PrestigeSociety\Forms\FormList\Directions;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\UIForms\SimpleForm;
class DirectionsForm extends FormHandler{
        protected bool $sent = false;

        public function send(Player $player){
                $dirs = $this->core->module_loader->directions->getDirectionsInWorld($player->getWorld()->getDisplayName());
                $form = new SimpleForm($this);

                $form->setTitle(RandomUtils::colorMessage("&8&lCHOOSE DIRECTION"));

                if(count($dirs) <= 0){
                        $form->setContent(RandomUtils::colorMessage("No directions found in this world."));
                        $form->setButton(RandomUtils::colorMessage("&8close"));
                        $form->send($player);
                        return;
                }

                foreach($dirs as $name => $dir){
                        $form->setButton(RandomUtils::colorMessage("&8$name\n&7- &8tap to get directions &7-"));
                }

                $this->sent = true;

                $form->send($player);
                $this->setData(array_keys($dirs));
        }

        public function handleResponse(Player $player, $formData){
                if(!$this->sent) return;

                $dirs = $this->getData();
                $dir = $dirs[$formData];

                $item = VanillaItems::COMPASS();
                $item->setCustomName(RandomUtils::colorMessage("&r&l&8» &edirections &7to &e$dir\n&7right click to cancel"));

                if(!$player->getInventory()->canAddItem($item)){
                        $player->sendMessage(RandomUtils::colorMessage($this->core->getMessage("directions", "inventory_full")));
                        return;
                }

                $player->getInventory()->addItem($item);

                $this->core->module_loader->directions->setCompass($player, $item);
                $this->core->module_loader->directions->giveDirections($dir, $player);

                $msg = $this->core->getMessage("directions", "directions_start");
                $msg = str_replace("@direction", $dir, $msg);
                $player->sendMessage(RandomUtils::colorMessage($msg));
        }
}