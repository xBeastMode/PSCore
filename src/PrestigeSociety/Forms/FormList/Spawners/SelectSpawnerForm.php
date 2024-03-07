<?php
namespace PrestigeSociety\Forms\FormList\Spawners;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\UIForms\SimpleForm;
class SelectSpawnerForm extends FormHandler{
        protected array $spawners = [
            'Chicken',
            'Cow',
            'Blaze',
            'Iron Golem',
            'Pig',
            'Sheep',
            'Skeleton',
            'Zombie',
            'Zombie Pigman'
        ];

        public function send(Player $player){
                $ui = new SimpleForm($this);
                $ui->setTitle(RandomUtils::colorMessage("&c&lMOB SPAWNERS"));

                foreach($this->spawners as $spawner){
                        $name = str_replace(" ", "_", strtolower($spawner));
                        $cost = $this->core->getConfig()->get('spawners_cost')[$name];

                        $ui->setButton(RandomUtils::colorMessage("&7- &8" . $spawner . " &7-\n&8$$cost"));
                }

                $ui->send($player);
        }

        public function handleResponse(Player $player, $formData){
                $this->core->module_loader->form_manager->sendForm($this->core->module_loader->spawners->CONFIRM_PURCHASE_ID, $player, $formData);
        }
}