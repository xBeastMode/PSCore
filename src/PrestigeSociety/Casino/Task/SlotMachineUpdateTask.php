<?php
namespace PrestigeSociety\Casino\Task;
use pocketmine\block\inventory\ChestInventory;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\ConsoleUtils;
use PrestigeSociety\Core\Utils\RandomUtils;
class SlotMachineUpdateTask extends Task{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;
        /** @var Player */
        protected Player $player;
        /** @var ChestInventory */
        protected ChestInventory $inventory;
        /** @var Item[] */
        protected array $items = [];
        /** @var string[] */
        protected array $commands = [];
        /** @var string */
        protected string $name;

        /** @var int[]|Item[] */
        protected array $spinData = [];
        protected int $spins = 0;

        /** @var bool */
        protected bool $same = true;
        /** @var int */
        protected int $index = -1;

        /** @var int */
        protected int $spinIndex = 0;

        /**
         *
         * MineResetter constructor.
         *
         * @param PrestigeSocietyCore $core
         * @param Player              $player
         * @param ChestInventory      $inventory
         * @param array               $items
         * @param array               $commands
         * @param string              $name
         */
        public function __construct(PrestigeSocietyCore $core, Player $player, ChestInventory $inventory, array $items, array $commands, string $name){
                $this->core = $core;
                $this->player = $player;
                $this->inventory = $inventory;
                $this->items = $items;
                $this->commands = $commands;
                $this->name = $name;
        }

        /**
         * Actions to execute when run
         *
         * @return void
         */
        public function onRun(): void{
                ++$this->spins;

                if(!$this->player->isOnline() || $this->player->isClosed()){
                        $this->core->module_loader->casino->endSpin($this->player, false);
                        $this->getHandler()->cancel();
                        return;
                }

                if($this->spins <= 30){
                        if(++$this->spinIndex >= count($this->items)){
                                $this->spinIndex = 0;
                        }

                        //RandomUtils::playSound("note.pling", $this->player);
                        RandomUtils::playSound("random.click", $this->player, 500, 1, true);

                        $item = $this->items[$this->spinIndex];
                        if($this->spins < 10){
                                $this->inventory->setItem(12, $item);
                                return;
                        }
                        if($this->spins === 10){
                                $this->spinIndex = array_rand($this->items);
                                $item = $this->items[$this->spinIndex];

                                $this->inventory->setItem(12, $item);

                                $this->spinData[] = $this->spinIndex;
                                $this->spinData[] = $item;
                        }

                        $item = $this->items[$this->spinIndex];
                        if($this->spins < 20){
                                $this->inventory->setItem(13, $item);
                                return;
                        }
                        if($this->spins === 20){
                                if(mt_rand(1, 2) === 1){
                                        $this->spinIndex = array_rand($this->items);
                                        $item = $this->items[$this->spinIndex];

                                        $this->inventory->setItem(13, $item);
                                        $this->spinData[] = $item;
                                }else{
                                        $item = $this->spinData[1];

                                        $this->inventory->setItem(13, $item);
                                        $this->spinData[] = $item;
                                }
                        }

                        $item = $this->items[$this->spinIndex];
                        if($this->spins < 30){
                                $this->inventory->setItem(14, $item);
                                return;
                        }

                        if($this->spins === 30){
                                if(mt_rand(1, 2) === 1){
                                        $this->spinIndex = array_rand($this->items);
                                        $item = $this->items[$this->spinIndex];

                                        $this->inventory->setItem(13, $item);
                                        $this->spinData[] = $item;
                                }else{
                                        $item = $this->spinData[2];

                                        $this->inventory->setItem(13, $item);
                                        $this->spinData[] = $item;
                                }
                        }
                }

                if($this->index === -1){
                        $this->index = array_shift($this->spinData);
                        $item = null;

                        foreach($this->spinData as $v){
                                if($item === null){
                                        $item = $v;
                                        continue;
                                }

                                if(!$v->equals($item, true, false)){
                                        $this->same = false;
                                        break;
                                }
                        }
                }

                if($this->same){
                        if($this->spins < 36){
                                if($this->spins === 31){
                                        RandomUtils::playSound("mob.villager.yes", $this->player, 500, 1, true);
                                }

                                if($this->spins % 2 === 0){
                                        $air = VanillaItems::AIR();

                                        $this->inventory->setItem(12, $air);
                                        $this->inventory->setItem(13, $air);
                                        $this->inventory->setItem(14, $air);
                                }else{
                                        $this->inventory->setItem(12, $this->spinData[0]);
                                        $this->inventory->setItem(13, $this->spinData[1]);
                                        $this->inventory->setItem(14, $this->spinData[2]);
                                }
                                return;
                        }

                        $this->getHandler()->cancel();
                        $this->core->module_loader->casino->endSpin($this->player, true);
                        $commands = explode(";", RandomUtils::colorMessage($this->commands[$this->index]));
                        foreach($commands as $command){
                                ConsoleUtils::dispatchCommandAsConsole(str_replace("@player", $this->player->getName(), $command));
                        }

                        $message = $this->core->getMessage('casino', 'won_slot_machine');
                        $message = str_replace(["@player", "@machine"], [$this->player->getName(), $this->name], $message);
                        $this->core->module_loader->casino->broadcastMessage(RandomUtils::colorMessage($message));
                }else{
                        RandomUtils::playSound("mob.villager.no", $this->player, 500, 1, true);
                        $this->getHandler()->cancel();
                        $this->core->module_loader->casino->endSpin($this->player, false);

                        $message = $this->core->getMessage('casino', 'lost_slot_machine');
                        $message = str_replace(["@player", "@machine"], [$this->player->getName(), $this->name], $message);
                        $this->core->module_loader->casino->broadcastMessage(RandomUtils::colorMessage($message));
                }
        }
}