<?php


namespace PrestigeSociety\Forms\FormList\Stones;
use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\ItemFactory;
use pocketmine\math\AxisAlignedBB;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\ProtectionStones\Stone;
use PrestigeSociety\ProtectionStones\StonesListener;
use PrestigeSociety\UIForms\SimpleForm;
class ConfirmBuyStoneForm extends FormHandler{
        public function send(Player $player){
                $name = $this->getData()[0];
                $size = $this->getData()[1];

                $cost = (int) $this->core->module_loader->protection_stones->stones_config["cost_per_block"];
                $finalCost = $cost * $size;

                $content = "===========================\n";
                $content .= "&7name: &f$name\n";
                $content .= "&7bounds: &f$size blocks\n";
                $content .= "&7cost per block: &f$cost\n";
                $content .= "&7final cost: &f$finalCost\n";
                $content .= "===========================\n";

                $ui = new SimpleForm($this);
                $ui->setTitle(RandomUtils::colorMessage("&8&lCONFIRM STONE BOUNDS"));
                $ui->setContent(RandomUtils::colorMessage($content));
                $ui->setButton(RandomUtils::colorMessage("&8yes"));
                $ui->setButton(RandomUtils::colorMessage("&8no"));
                $ui->send($player);
        }

        public function cancel(Player $player, Block $block){
                $block->getPosition()->getWorld()->setBlock($block->getPosition(), VanillaBlocks::AIR());
                $player->getInventory()->addItem(ItemFactory::getInstance()->get(StonesListener::STONE_META));
        }

        public function handleNull(Player $player){
                /** @var Block $block */
                $block = $this->getData()[3];
                $this->cancel($player, $block);

                $msg = $this->core->getMessage("stones", "stone_place_cancel");
                $msg = RandomUtils::colorMessage($msg);
                $player->sendMessage($msg);
        }

        public function handleResponse(Player $player, $formData){
                if($formData === 0){
                        $name = $this->getData()[0];
                        $size = $this->getData()[1];

                        /** @var Block $block */
                        $block = $this->getData()[2];
                        $position = $block->getPosition();

                        $cost = (int)$this->core->module_loader->protection_stones->stones_config["cost_per_block"];
                        $cost = $cost * $size;
                        $money = $this->core->module_loader->economy->getMoney($player);

                        if($money < $cost){
                                $msg = $this->core->getMessage("stones", "not_enough_money");
                                $msg = RandomUtils::colorMessage($msg);
                                $msg = str_replace(["@money", "@cost"], [$money, $cost], $msg);
                                $player->sendMessage($msg);

                                $this->cancel($player, $block);
                                return;
                        }

                        $this->core->module_loader->economy->subtractMoney($player, $cost);

                        $minX = $position->x - $size;
                        $minZ = $position->z - $size;
                        $maxX = $position->x + $size;
                        $maxZ = $position->z + $size;

                        $stone = new Stone($name, $player->getName(), $position, new AxisAlignedBB($minX, 0, $minZ, $maxX, $position->getWorld()->getMaxY(), $maxZ), []);

                        if($this->core->module_loader->protection_stones->stoneExists($stone)){
                                $msg = $this->core->getMessage("stones", "stone_exists");
                                $msg = RandomUtils::colorMessage($msg);
                                $player->sendMessage($msg);

                                $this->cancel($player, $block);
                                return;
                        }

                        $this->core->module_loader->protection_stones->saveStone($stone);

                        $msg = $this->core->getMessage("stones", "stone_placed");
                        $msg = RandomUtils::colorMessage($msg);
                        $msg = str_replace([
                            "@name",
                            "@player",
                            "@size",
                            "@cost",
                            "@x",
                            "@y",
                            "@z",
                            "@minX",
                            "@minZ",
                            "@maxX",
                            "@maxZ"
                        ], [
                            $name,
                            $player->getName(),
                            $size,
                            $cost,
                            $position->x,
                            $position->y,
                            $position->z,
                            $minX,
                            $minZ,
                            $maxX,
                            $maxZ
                        ], $msg);
                        $player->sendMessage($msg);
                }
        }
}