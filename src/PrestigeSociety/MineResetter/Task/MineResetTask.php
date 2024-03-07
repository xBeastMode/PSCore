<?php
namespace PrestigeSociety\MineResetter\Task;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\world\format\Chunk;
use pocketmine\world\format\io\FastChunkSerializer;
use pocketmine\world\format\SubChunk;
use pocketmine\world\World;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;

class MineResetTask extends AsyncTask{
        /** @var string */
        protected string $areaName;
        /** @var string */
        protected string $areaData;

        /** @var string $chunks */
        protected string $chunks;
        /** @var string */
        protected string $levelName;
        /** @var bool */
        protected bool $singleMine = false;

        /**
         * MineResetTask constructor.
         *
         * @param string $areaName
         * @param array  $areaData
         * @param array  $chunks
         * @param string $levelName
         * @param bool   $singleMine
         */
        public function __construct(string $areaName, array $areaData, array $chunks, string $levelName, bool $singleMine = false){
                $this->areaName = $areaName;
                $this->areaData = serialize($areaData);
                $this->chunks = serialize($chunks);
                $this->levelName = $levelName;
                $this->singleMine = $singleMine;
        }

        /**
         * Actions to execute when run
         *
         * @return void
         */
        public function onRun(): void{
                /** @var array $areaData */
                $areaData = unserialize($this->areaData);
                /** @var string[] $chunks */
                $chunks = unserialize($this->chunks);

                foreach($chunks as $hash => $binary){
                        $chunks[$hash] = FastChunkSerializer::deserializeTerrain($binary);
                }

                if(isset($areaData["extra_data"]["mine"])){
                        $blocks = (array)$areaData["extra_data"]["blocks"];

                        $mineBlocks = [];

                        $minX = min($areaData["min"][0], $areaData["max"][0]);
                        $minY = min($areaData["min"][1], $areaData["max"][1]);
                        $minZ = min($areaData["min"][2], $areaData["max"][2]);
                        $maxX = max($areaData["min"][0], $areaData["max"][0]);
                        $maxY = max($areaData["min"][1], $areaData["max"][1]);
                        $maxZ = max($areaData["min"][2], $areaData["max"][2]);

                        $blockCount = (($maxX - $minX) * ($maxZ - $minZ));

                        if(($maxY - $minY) > 0){
                                $blockCount *= ($maxY - $minY);
                        }

                        $start = 0;

                        foreach($blocks as $key => $percentage){

                                $key = explode(":", $key);
                                $percentage = (($blockCount / 100) * (int)$percentage);

                                for($i = $start; $i < ($start + $percentage); ++$i){
                                        $mineBlocks[$i][0] = $key[0];
                                        $mineBlocks[$i][1] = $key[1] ?? 0;
                                }
                                $start += $percentage;

                        }

                        shuffle($mineBlocks);

                        $mineBlockCount = 0;

                        $currentChunkX = 0;
                        $currentChunkZ = 0;

                        /** @var Chunk $currentChunk */
                        $currentChunk = null;

                        for($x = $minX; $x <= $maxX; ++$x){

                                $chunkX = $x >> 4;

                                for($z = $minZ; $z <= $maxZ; ++$z){

                                        $chunkZ = $z >> 4;

                                        if($currentChunk === null || $chunkX !== $currentChunkX || $chunkZ !== $currentChunkZ){
                                                $currentChunkX = $chunkX;
                                                $currentChunkZ = $chunkZ;

                                                $hash = World::chunkHash($chunkX, $chunkZ);
                                                $currentChunk = $chunks[$hash];

                                                if($currentChunk === null){
                                                        continue;
                                                }
                                        }

                                        for($y = $minY; $y <= $maxY; ++$y){
                                                $blockId = (int) $mineBlocks[$mineBlockCount][0];
                                                $blockMeta = (int) $mineBlocks[$mineBlockCount][1];
                                                $fullBlockId = RandomUtils::legacyToInternalStateId($blockId, $blockMeta);

                                                $currentChunk->setFullBlock($x & Chunk::COORD_MASK, $y, $z & Chunk::COORD_MASK, $fullBlockId);

                                                ++$mineBlockCount;

                                                if($mineBlockCount >= $blockCount){
                                                        $mineBlockCount = 0;
                                                }
                                        }
                                }
                        }
                }

                $this->setResult($chunks);
        }

        public function onCompletion(): void{
                $result = $this->getResult();
                $core = PrestigeSocietyCore::getInstance();
                $server = $core->getServer();

                if($core instanceof PrestigeSocietyCore){
                        $level = $server->getWorldManager()->getWorldByName($this->levelName);
                        if($level instanceof World){
                                foreach($result as $hash => $chunk){
                                        World::getXZ($hash, $x, $z);
                                        $level->setChunk($x, $z, $chunk);
                                }
                        }
                        if($this->singleMine){
                                $core->module_loader->mine_resetter->notifyCompletion($this->areaName);
                        }else{
                                $core->module_loader->mine_resetter->notifyCompletion();
                        }
                }
        }
}