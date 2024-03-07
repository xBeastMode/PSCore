<?php
namespace PrestigeSociety\Forms\FormList\Stones;
use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\ItemFactory;
use pocketmine\math\AxisAlignedBB;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\ProtectionStones\StonesListener;
use PrestigeSociety\UIForms\CustomForm;
class SelectStoneBoundsForm extends FormHandler{
        public function send(Player $player){
                $ui = new CustomForm($this);
                $cost = (int) $this->core->module_loader->protection_stones->stones_config["cost_per_block"];

                $ui->setTitle(RandomUtils::colorMessage("&8&lSELECT STONE BOUNDS"));
                $ui->setInput(RandomUtils::colorMessage("&7stone name&r"));
                $ui->setSlider(RandomUtils::colorMessage("&7stone protection size&r"), 5, 100);
                $ui->send($player);
        }

        public function cancel(Player $player, Block $block){
                $block->getPosition()->getWorld()->setBlock($block->getPosition(), VanillaBlocks::AIR());
                $player->getInventory()->addItem(ItemFactory::getInstance()->get(StonesListener::STONE_META));
        }

        public function handleNull(Player $player){
                /** @var Block $block */
                $block = $this->getData();
                $this->cancel($player, $block);

                $msg = $this->core->getMessage("stones", "stone_place_cancel");
                $msg = RandomUtils::colorMessage($msg);
                $player->sendMessage($msg);
        }

        public function handleResponse(Player $player, $formData){
                /** @var Block $block */
                $block = $this->getData();
                $position = $block->getPosition();

                $size = $formData[1];

                $minX = $position->x - $size;
                $minZ = $position->z - $size;
                $maxX = $position->x + $size;
                $maxZ = $position->z + $size;

                $bounds = new AxisAlignedBB($minX, 0, $minZ, $maxX, $position->getWorld()->getMaxY(), $maxZ);
                if($this->core->module_loader->protection_stones->boundsCollideWithUnaffiliatedStones($player, $bounds) && !$player->hasPermission("stones.intersect.bypass")){
                        $msg = $this->core->getMessage("stones", "bounds_intersect");
                        $msg = RandomUtils::colorMessage($msg);
                        $player->sendMessage($msg);

                        $this->cancel($player, $block);
                        return;
                }

                $name = $formData[0];
                if(strlen($name) < 1){
                        $msg = $this->core->getMessage("stones", "invalid_name");
                        $msg = RandomUtils::colorMessage($msg);
                        $player->sendMessage($msg);

                        $this->cancel($player, $block);
                        return;
                }
                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->CONFIRM_BUY_STONE_ID, $player, [$name, $size, $block]);
        }
}