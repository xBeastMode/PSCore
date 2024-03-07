<?php
namespace PrestigeSociety\MineResetter;
use pocketmine\block\Block;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Human;
use pocketmine\math\AxisAlignedBB;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\scheduler\TaskHandler;
use pocketmine\world\format\Chunk;
use pocketmine\world\format\io\FastChunkSerializer;
use pocketmine\world\World;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\MineResetter\Entity\ResetMineEntity;
use PrestigeSociety\MineResetter\Task\EmptyMineTickerTask;
use PrestigeSociety\MineResetter\Task\MineResetTask;
use PrestigeSociety\MineResetter\Task\MineTickerTask;
use pocketmine\entity\EntityDataHelper as Helper;
class MineResetter{
        /** @var array[] */
        protected array $loaded_mines = [];
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /** @var TaskHandler */
        protected TaskHandler $task;

        /** @var bool */
        protected bool $resetting = true;
        /** @var int */
        protected int $reset_count = 0;

        /**
         * MineResetter constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;

                if(RandomUtils::toBool($core->getConfig()->get("mines")["auto_reset"])){
                        $this->core->getScheduler()->scheduleRepeatingTask(new EmptyMineTickerTask($this->core), 20 * intval($core->getConfig()->get("mines")["auto_reset_time"]));
                }

                EntityFactory::getInstance()->register(ResetMineEntity::class, function(World $world, CompoundTag $nbt) : ResetMineEntity{
                        return new ResetMineEntity(Helper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $nbt);
                }, ["ResetMineEntity"]);

                $this->core->getServer()->getPluginManager()->registerEvents(new MineResetterListener($core), $core);
        }

        public function reloadMines(): void{
                $this->loaded_mines = $this->core->module_loader->land_protector->getAreasWithData("mine");
                $this->startTickingMines();
        }

        /**
         * @param string $name
         */
        public function loadMine(string $name): void{
                if($this->core->module_loader->land_protector->areaHasExtraData($name, "mine")){
                        $this->loaded_mines[$name] = $this->core->module_loader->land_protector->getAreaData($name);
                }
        }

        /**
         * @param string $name
         */
        public function unloadMine(string $name): void{
                if(isset($this->loaded_mines[$name])){
                        unset($this->loaded_mines[$name]);
                }
        }

        public function startTickingMines(): void{
                $handler = $this->core->getScheduler()->scheduleRepeatingTask(new MineTickerTask($this->core, (int)$this->core->getConfig()->get("mines")["reset_time"]), 20);
                $this->task = $handler;
        }

        /**
         * @param string|null $area
         *
         * @return bool
         */
        public function notifyRestart(string $area = null): bool{
                if($area === null){
                        $message = $this->core->getMessage("mines", "all_mine_reset_start");
                        $this->core->getServer()->broadcastMessage(RandomUtils::colorMessage($message));

                        $this->startReset();
                        return true;
                }else{
                        if(isset($this->loaded_mines[$area])){
                                $message = $this->core->getMessage("mines", "mine_reset_start");
                                $message = str_replace("@mine", $area, $message);
                                $this->core->getServer()->broadcastMessage(RandomUtils::colorMessage($message));

                                $this->startReset($area);
                                return true;
                        }
                }
                return false;
        }

        /**
         * @param string|null $area
         *
         * @return bool
         */
        public function notifyCompletion(string $area = null): bool{
                if($area === null){
                        if($this->resetting && (++$this->reset_count >= count($this->loaded_mines))){
                                $message = $this->core->getMessage("mines", "all_mine_reset_completion");
                                $this->core->getServer()->broadcastMessage(RandomUtils::colorMessage($message));

                                $this->startTickingMines();
                                $this->resetting = false;
                                $this->reset_count = 0;
                                return true;
                        }
                        return false;
                }else{
                        if(isset($this->loaded_mines[$area])){
                                $message = $this->core->getMessage("mines", "mine_reset_completion");
                                $message = str_replace("@mine", $area, $message);
                                $this->core->getServer()->broadcastMessage(RandomUtils::colorMessage($message));
                                return true;
                        }
                }
                return false;
        }

        /**
         * @param string|null $area
         *
         * @return bool
         */
        public function startReset(string $area = null): bool{
                if($area === null){
                        $this->resetting = true;
                        foreach($this->loaded_mines as $name => $mine){
                                $this->scheduleMineReset($name, false);
                        }
                        $this->task->cancel();
                        return true;
                }else{
                        $this->scheduleMineReset($area, true);
                }
                return false;
        }

        /**
         * @param string $area
         * @param bool   $singleMine
         */
        protected function scheduleMineReset(string $area, bool $singleMine = false): void{
                if(isset($this->loaded_mines[$area])){
                        $areaData = $this->loaded_mines[$area];

                        $minX = min($areaData["min"][0], $areaData["max"][0]);
                        $minY = min($areaData["min"][1], $areaData["max"][1]);
                        $minZ = min($areaData["min"][2], $areaData["max"][2]);
                        $maxX = max($areaData["min"][0], $areaData["max"][0]);
                        $maxY = max($areaData["min"][1], $areaData["max"][1]);
                        $maxZ = max($areaData["min"][2], $areaData["max"][2]);

                        if(!$this->core->getServer()->getWorldManager()->isWorldLoaded($areaData["world"])){
                                $this->core->getServer()->getWorldManager()->loadWorld($areaData["world"]);
                        }

                        $level = $this->core->getServer()->getWorldManager()->getWorldByName($areaData["world"]);
                        if($level instanceof World){
                                foreach($level->getNearbyEntities(new AxisAlignedBB($minX, $minY, $minZ, $maxX, $maxY, $maxZ)) as $entity){
                                        if($entity instanceof Player){
                                                $entity->teleport($level->getSafeSpawn());
                                        }
                                }

                                $chunks = [];
                                for($x = $minX; $x - 16 <= $maxX; $x += 16){
                                        for($z = $minZ; $z - 16 <= $maxZ; $z += 16){
                                                if(!$level->isChunkLoaded($x >> 4, $z >> 4)) $level->loadChunk($x >> 4, $z >> 4);

                                                $chunk = $level->getChunk($x >> 4, $z >> 4);
                                                $chunks[World::chunkHash($x >> 4, $z >> 4)] = FastChunkSerializer::serializeTerrain($chunk);
                                        }
                                }

                                $reset = new MineResetTask($area, $areaData, $chunks, $level->getDisplayName(), $singleMine);
                                $scheduler = $this->core->getServer()->getAsyncPool();

                                $scheduler->submitTask($reset);
                        }
                }
        }

        /**
         * @param string|null   $area
         * @param callable|null $callback
         *
         * @return bool
         */
        public function resetEmptyMines(string $area = null, callable $callback = null): bool{
                if($area !== null){
                        $areaData = $this->loaded_mines[$area] ?? null;

                        if($areaData === null) return false;

                        $minX = min($areaData["min"][0], $areaData["max"][0]);
                        $minY = min($areaData["min"][1], $areaData["max"][1]);
                        $minZ = min($areaData["min"][2], $areaData["max"][2]);
                        $maxX = max($areaData["min"][0], $areaData["max"][0]);
                        $maxY = max($areaData["min"][1], $areaData["max"][1]);
                        $maxZ = max($areaData["min"][2], $areaData["max"][2]);

                        if(!$this->core->getServer()->getWorldManager()->isWorldLoaded($areaData["world"])) return false;

                        $level = $this->core->getServer()->getWorldManager()->getWorldByName($areaData["world"]);
                        if($level instanceof World){
                                $chunks = [];
                                for($x = $minX; $x - 16 <= $maxX; $x += 16){
                                        for($z = $minZ; $z - 16 <= $maxZ; $z += 16){
                                                if(!$level->isChunkLoaded($x >> 4, $z >> 4)) $level->loadChunk($x >> 4, $z >> 4);

                                                $chunk = $level->getChunk($x >> 4, $z >> 4);
                                                $chunks[World::chunkHash($x >> 4, $z >> 4)] = FastChunkSerializer::serializeTerrain($chunk);
                                        }
                                }

                                $chunks = serialize($chunks);
                                $this->core->module_loader->async_manager->submitAsyncCallback(function () use ($chunks, $minX, $minY, $minZ, $maxX, $maxY, $maxZ){
                                        /** @var Chunk[] $class */
                                        $chunks = unserialize($chunks);

                                        foreach($chunks as $hash => $binary){
                                                $chunks[$hash] = FastChunkSerializer::deserializeTerrain($binary);
                                        }

                                        for($x = $minX; $x < $maxX; $x++){
                                                for($z = $minZ; $z < $maxZ; $z++){
                                                        /** @var Chunk $chunk */
                                                        $chunk = $chunks[World::chunkHash($x >> 4, $z >> 4)] ?? null;
                                                        if($chunk !== null){
                                                                for($y = $minY; $y < $maxY; $y++){
                                                                        if(($chunk->getFullBlock($x & 0x0f, $y, $z & 0x0f) >> Block::INTERNAL_METADATA_BITS) !== 0){
                                                                                return false;
                                                                        }
                                                                }
                                                                unset($chunks[World::chunkHash($x >> 4, $z >> 4)]);
                                                        }
                                                }
                                        }
                                        return true;
                                }, function ($empty) use ($area, $callback){
                                        if($callback !== null){
                                                $callback($area, $empty);
                                                return;
                                        }

                                        if($empty){
                                                PrestigeSocietyCore::getInstance()->module_loader->mine_resetter->notifyRestart($area);
                                        }
                                });
                        }
                }else{
                        $data = [];

                        foreach($this->loaded_mines as $name => $areaData){
                                $minX = min($areaData["min"][0], $areaData["max"][0]);
                                $minY = min($areaData["min"][1], $areaData["max"][1]);
                                $minZ = min($areaData["min"][2], $areaData["max"][2]);
                                $maxX = max($areaData["min"][0], $areaData["max"][0]);
                                $maxY = max($areaData["min"][1], $areaData["max"][1]);
                                $maxZ = max($areaData["min"][2], $areaData["max"][2]);

                                $level = null;
                                if(!$this->core->getServer()->getWorldManager()->isWorldLoaded($areaData["world"])) continue;

                                $level = $this->core->getServer()->getWorldManager()->getWorldByName($areaData["world"]);
                                if($level instanceof World){
                                        $chunks = [];
                                        for($x = $minX; $x - 16 <= $maxX; $x += 16){
                                                for($z = $minZ; $z - 16 <= $maxZ; $z += 16){
                                                        if(!$level->isChunkLoaded($x >> 4, $z >> 4)) $level->loadChunk($x >> 4, $z >> 4);

                                                        $chunk = $level->getChunk($x >> 4, $z >> 4);
                                                        $chunks[World::chunkHash($x >> 4, $z >> 4)] = FastChunkSerializer::serializeTerrain($chunk);
                                                }
                                        }

                                        $chunks = serialize($chunks);
                                        $data[] = [$name, $chunks, $minX, $minY, $minZ, $maxX, $maxY, $maxZ];
                                }
                        }

                        if(!empty($data)){
                                $data = serialize($data);
                                $this->core->module_loader->async_manager->submitAsyncCallback(function () use ($data){
                                        $data = unserialize($data);
                                        $output = [];
                                        foreach($data as $dat){
                                                list($name, $chunks, $minX, $minY, $minZ, $maxX, $maxY, $maxZ) = $dat;

                                                /** @var Chunk[] $class */
                                                $chunks = unserialize($chunks);

                                                foreach($chunks as $hash => $binary){
                                                        $chunks[$hash] = FastChunkSerializer::deserializeTerrain($binary);
                                                }

                                                $output[$name] = true;

                                                for($x = $minX; $x < $maxX; $x++){
                                                        for($z = $minZ; $z < $maxZ; $z++){
                                                                /** @var Chunk $chunk */
                                                                $chunk = $chunks[World::chunkHash($x >> 4, $z >> 4)] ?? null;

                                                                if($chunk !== null){
                                                                        for($y = $minY; $y < $maxY; $y++){
                                                                                if(($chunk->getFullBlock($x & 0x0f, $y, $z & 0x0f) >> Block::INTERNAL_METADATA_BITS) !== 0){
                                                                                        $output[$name] = false;
                                                                                }
                                                                        }
                                                                        unset($chunks[World::chunkHash($x >> 4, $z >> 4)]);
                                                                }
                                                        }
                                                }
                                        }
                                        return $output;
                                }, function ($data) use ($callback){
                                        foreach($data as $name => $empty){
                                                if($callback !== null){
                                                        $callback($name, $empty);
                                                        continue;
                                                }

                                                if($empty){
                                                        PrestigeSocietyCore::getInstance()->module_loader->mine_resetter->notifyRestart($name);
                                                }
                                        }
                                });
                        }
                }

                return true;
        }
}