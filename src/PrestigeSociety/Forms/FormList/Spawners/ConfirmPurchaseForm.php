<?php
namespace PrestigeSociety\Forms\FormList\Spawners;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Forms\FormHandler\FormHandler;
use PrestigeSociety\UIForms\SimpleForm;
class ConfirmPurchaseForm extends FormHandler{
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

        protected array $entityIds = [
            'chicken' => EntityIds::CHICKEN,
            'cow' => EntityIds::COW,
            'blaze' => EntityIds::BLAZE,
            'iron_golem' => EntityIds::IRON_GOLEM,
            'pig' => EntityIds::PIG,
            'sheep' => EntityIds::SHEEP,
            'skeleton' => EntityIds::SKELETON,
            'zombie' => EntityIds::ZOMBIE,
            'zombie_pigman' => EntityIds::ZOMBIE_PIGMAN
        ];

        /**
         * @param Player $player
         */
        public function send(Player $player){
                $spawner = $this->spawners[$this->getData()];
                $name = str_replace(" ", "_", strtolower($spawner));
                $cost = $this->core->getConfig()->get('spawners_cost')[$name];

                $ui = new SimpleForm($this);
                $ui->setTitle(RandomUtils::colorMessage("&c&lCONFIRM PURCHASE"));
                $content = "";
                $content .= "&c===========================\n";
                $content .= "&7type: &f" . $spawner . " spawner\n";
                $content .= "&7cost: &f" . $cost . "\n";
                $content .= "&c===========================\n";
                $ui->setContent(RandomUtils::colorMessage($content));
                $ui->setButton(RandomUtils::colorMessage("&ayes"));
                $ui->setButton(RandomUtils::colorMessage("&cno"));
                $ui->send($player);

                $this->setData($spawner);
        }

        /**
         * @param Player $player
         * @param        $formData
         */
        public function handleResponse(Player $player, $formData){
                if($formData === 0){
                        $spawner = $this->getData();
                        $name = str_replace(" ", "_", strtolower($spawner));

                        $cost = $this->core->getConfig()->get('spawners_cost')[$name];
                        $money = $this->core->module_loader->economy->getMoney($player);

                        if($money < $cost){
                                $message = $this->core->getMessage('spawners', 'not_enough_money');
                                $message = str_replace(["@money", "@cost"], [$money, $cost], $message);
                                $player->sendMessage(RandomUtils::colorMessage($message));
                                return;
                        }

                        $item = ItemFactory::getInstance()->get(ItemIds::MOB_SPAWNER);
                        $item->getNamedTag()->setInt("entityId", $this->entityIds[$name]);
                        $item->setCustomName(RandomUtils::colorMessage("&r&c&l" . strtoupper($spawner) . " SPAWNER\n&r&fplace to activate"));
                        $item->setLore([RandomUtils::colorMessage("&6- place it where you want to activate it."), RandomUtils::colorMessage("&6- contact staff member if you want it moved.")]);

                        if(!$player->getInventory()->canAddItem($item)){
                                $message = $this->core->getMessage('spawners', 'cannot_add_item');
                                $player->sendMessage(RandomUtils::colorMessage($message));
                                return;
                        }

                        $this->core->module_loader->economy->subtractMoney($player, $cost);

                        $player->getInventory()->addItem($item);

                        $message = $this->core->getMessage('spawners', 'bought_spawner');
                        $message = str_replace(["@spawner", "@cost"], [$spawner, $cost], $message);
                        $player->sendMessage(RandomUtils::colorMessage($message));
                }
        }
}