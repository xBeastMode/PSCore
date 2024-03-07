<?php
namespace PrestigeSociety\Core\Utils;
use pocketmine\block\BaseSign;
use pocketmine\block\EnchantingTable;
use pocketmine\block\tile\Chest;
use pocketmine\block\tile\EnchantTable;
use pocketmine\block\tile\Furnace;
use pocketmine\block\tile\Tile;
use pocketmine\block\utils\SignText;
use pocketmine\world\format\Chunk;
use pocketmine\world\World;
use PrestigeSociety\Core\PrestigeSocietyCore;
class TileUtils{
        /**
         * @return Tile[]
         */
        public static function getServerTiles(): array{
                return StringUtils::returnArrayOfMultidimensionalArray(array_map(function (World $level){
                        return self::getLevelTiles($level);
                }, PrestigeSocietyCore::getInstance()->getServer()->getWorldManager()->getWorlds()));
        }

        /**
         * @param World $level
         *
         * @return Tile[]
         */
        public static function getLevelTiles(World $level): array{
                return array_map(function (Chunk $chunk){ return $chunk->getTiles(); }, $level->getLoadedChunks());
        }

        /**
         * @return Chest[]
         */
        public static function getChestTiles(): array{
                return array_filter(static::getServerTiles(), function ($var){ return $var instanceof Chest; });
        }

        /**
         * @return EnchantTable[]
         */
        public static function getEnchantingTables(): array{
                return array_filter(static::getServerTiles(), function ($var){ return $var instanceof EnchantingTable; });
        }

        /**
         * @return Furnace[]
         */
        public static function getFurnaces(): array{
                return array_filter(static::getServerTiles(), function ($var){ return $var instanceof Furnace; });
        }

        /**
         * @return BaseSign[]
         */
        public static function getSignTiles(): array{
                return array_filter(static::getServerTiles(), function ($var){ return $var instanceof BaseSign; });
        }

        /**
         * @param BaseSign     $tile
         * @param string[] $text
         *
         * @return bool
         */
        public static function setSignTileText(BaseSign $tile, array $text): bool{
                $tile->setText(new SignText($text));
                return true;
        }
}