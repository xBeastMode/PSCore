<?php
namespace PrestigeSociety\Core;
use pocketmine\player\Player;
use pocketmine\world\particle\FloatingTextParticle;
use pocketmine\world\Position;
use pocketmine\world\World;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\Core\Utils\TileUtils;

class BlockParticles{
        /** @var FloatingTextParticle[] */
        protected static array $block_positions = [];

        /**
         * @param Position $block
         */
        public static function removeParticle(Position $block){
                if(isset(self::$block_positions[RandomUtils::positionToString($block)])){
                        $particle = self::$block_positions[RandomUtils::positionToString($block)];
                        $particle->setInvisible();

                        $block->getWorld()->addParticle($block->asPosition(), $particle);
                }
        }

        /**
         * @param Position $block
         * @param string   $title
         * @param string   $text
         */
        public static function addParticle(Position $block, string $title, string $text){
                self::removeParticle($block);

                $particle = new FloatingTextParticle($text, $title);
                self::$block_positions[RandomUtils::positionToString($block)] = $particle;

                $block->getWorld()->addParticle($block->round()->add(0, 2, 0), $particle);
        }

        /**
         * @param Position $block
         * @param string   $title
         * @param string   $text
         */
        public static function updateParticle(Position $block, string $title, string $text){
                if(isset(self::$block_positions[RandomUtils::positionToString($block)])){
                        $particle = self::$block_positions[RandomUtils::positionToString($block)];

                        $particle->setTitle($title);
                        $particle->setText($text);

                        $particle->setInvisible(false);
                        $block->getWorld()->addParticle($block->round()->add(0, 2, 0), $particle);
                }
        }

        /**
         * @param Position $block
         * @param Player   $player
         * @param bool     $remove
         */
        public static function sendParticle(Position $block, Player $player, bool $remove = false){
                if(isset(self::$block_positions[RandomUtils::positionToString($block)])){
                        $particle = self::$block_positions[RandomUtils::positionToString($block)];
                        $particle->setInvisible($remove);

                        $block->getWorld()->addParticle($block->asPosition(), $particle);
                }
        }

        /**
         * @param Player $player
         * @param World  $level
         * @param bool   $remove
         */
        public static function sendParticles(Player $player, World $level, bool $remove = false){
                foreach(TileUtils::getLevelTiles($level) as $tile){
                        self::sendParticle($tile->getPosition(), $player, $remove);
                }
        }
}