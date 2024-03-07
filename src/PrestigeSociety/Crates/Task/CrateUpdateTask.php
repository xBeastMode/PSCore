<?php
namespace PrestigeSociety\Crates\Task;
use pocketmine\block\inventory\ChestInventory;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\ConsoleUtils;
use PrestigeSociety\Core\Utils\RandomUtils;
class CrateUpdateTask extends Task{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;
        /** @var Player */
        protected Player $player;
        /** @var ChestInventory */
        protected ChestInventory $inventory;
        /** @var Item[] */
        protected array $items;
        /** @var string[] */
        protected array $commands;

        protected int $spins = 0;
        protected int $index = 0;

        /**
         * CrateUpdateTask constructor.
         *
         * @param PrestigeSocietyCore $core
         * @param Player              $player
         * @param ChestInventory      $inventory
         * @param array               $items
         * @param array               $commands
         */
        public function __construct(PrestigeSocietyCore $core, Player $player, ChestInventory $inventory, array $items, array $commands){
                $this->core = $core;
                $this->player = $player;
                $this->inventory = $inventory;
                $this->items = $items;
                $this->commands = $commands;
        }

        /**
         * Actions to execute when run
         *
         * @return void
         */
        public function onRun(): void{
                ++$this->spins;

                if(!$this->player->isOnline() || $this->player->isClosed()){
                        $this->core->module_loader->crates->endSpin($this->player, false);
                        $this->getHandler()->cancel();
                        return;
                }

                if($this->spins <= 30){
                        $this->index = array_rand($this->items);

                        $this->inventory->setItem(13, $this->items[$this->index]);
                        RandomUtils::playSound("random.click", $this->player, 500, 1, true);
                }

                if($this->spins > 30){
                        if($this->spins === 31){
                                RandomUtils::playSound("firework.twinkle", $this->player, 500, 1, true);
                        }

                        if($this->spins > 34){
                                $commands = $this->commands[$this->index];
                                foreach($commands as $command){
                                        $data = explode("$", $command);
                                        if($data[0] === "i"){
                                                $item = RandomUtils::parseItemsWithEnchantments([$data[1]])[0];
                                                $this->player->getInventory()->addItem($item);
                                        }elseif($data[0] === "cmd"){
                                                $command = $data[1];
                                                $command = str_replace("@player", $this->player->getName(), $command);

                                                ConsoleUtils::dispatchCommandAsConsole($command);
                                        }
                                }

                                $message = $this->core->getMessage("crates", "end_spin");
                                $message = str_replace(["@player", "@item"], [$this->player->getName(), $this->items[$this->index]->getName()], $message);
                                $this->core->module_loader->crates->broadcastMessage(RandomUtils::colorMessage($message));

                                $this->core->module_loader->crates->endSpin($this->player);
                                $this->getHandler()->cancel();
                        }else{
                                if($this->spins % 2 === 0){
                                        $this->inventory->setItem(13, VanillaItems::AIR());
                                }else{
                                        $this->inventory->setItem(13, $this->items[$this->index]);
                                }
                        }
                }
        }
}