<?php
namespace PrestigeSociety\Forms\FormList\Stones;
use pocketmine\block\VanillaBlocks;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\ProtectionStones\Stone;
use PrestigeSociety\UIForms\SimpleForm;
class ConfirmStoneDeletionForm extends FormHandler{
        public function send(Player $player){
                /** @var Stone $stone */
                $stone = $this->getData();

                $ui = new SimpleForm($this);
                $ui->setTitle(RandomUtils::colorMessage("&8&lDELETE STONE"));
                $ui->setContent(RandomUtils::colorMessage("&7are you sure you want to permanently delete &f" . $stone->getName() . "&7?\n&l&cYOU WILL NOT GET IT BACK."));
                $ui->setButton(RandomUtils::colorMessage("&8yes"));
                $ui->setButton(RandomUtils::colorMessage("&8no"));
                $ui->send($player);
        }

        public function handleResponse(Player $player, $formData){
                /** @var Stone $stone */
                $stone = $this->getData();

                if($formData === 0){
                        $msg = $this->core->getMessage("stones", "stone_deleted");
                        $msg = RandomUtils::colorMessage($msg);
                        $msg = str_replace(["@name"], [$stone->getName()], $msg);
                        $player->sendMessage($msg);

                        $stone->position->getWorld()->setBlock($stone->position, VanillaBlocks::AIR());
                        $this->core->module_loader->protection_stones->deleteStone($stone);
                }
        }
}