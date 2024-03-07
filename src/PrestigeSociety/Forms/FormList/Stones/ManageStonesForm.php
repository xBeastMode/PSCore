<?php
namespace PrestigeSociety\Forms\FormList\Stones;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\ProtectionStones\Stone;
use PrestigeSociety\UIForms\SimpleForm;
class ManageStonesForm extends FormHandler{
        public function send(Player $player){
                $stones = $this->core->module_loader->protection_stones->getPlayerStones($player);

                if(count($stones) < 1){
                        $msg = $this->core->getMessage("stones", "no_stones");
                        $msg = RandomUtils::colorMessage($msg);
                        $player->sendMessage($msg);
                        return;
                }

                $ui = new SimpleForm($this);
                $ui->setTitle(RandomUtils::colorMessage("&8&lMANAGE STONES"));
                foreach($stones as $stone){
                        $ui->setButton(RandomUtils::colorMessage("&7- &8" . $stone->getName() . " &7-"));
                }
                $ui->send($player);

                $this->setData($stones);
        }

        public function handleResponse(Player $player, $formData){
                /** @var Stone[] $stones */
                $stones = $this->getData();
                $stone = $stones[$formData];

                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->protection_stones->MANAGE_SINGLE_STONE_ID, $player, $stone);
        }
}