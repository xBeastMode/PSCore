<?php
namespace PrestigeSociety\Core\Utils;
use pocketmine\utils\BinaryStream;
class SkinUtils{
        /**
         * @param string $path
         *
         * @return null|string
         */
        public static function skinFromPNGFile(string $path): ?string{
                return TextureUtils::textureFromPNGFile($path);
        }

        /**
         * @param string $skin
         * 
         * @return string|null
         */
        public static function skinToGreyscale(string $skin): ?string{
                $stream = new BinaryStream($skin);
                $new_stream = new BinaryStream();

                $loop = match (strlen($skin)) {
                        64 * 32 * 4 => 64 * 32,
                        64 * 64 * 4 => 64 * 64,
                        128 * 128 * 4 => 128 * 128,
                        default => 0,
                };

                for($i = 0; $i < $loop; $i++){
                        $r = $stream->getByte() & 0xFF;
                        $g = $stream->getByte() & 0xFF;
                        $b = $stream->getByte() & 0xFF;
                        $a = $stream->getByte() & 0xFF;

                        $r = $g = $b = ($r + $g + $b) / 3;

                        $new_stream->putByte($r);
                        $new_stream->putByte($g);
                        $new_stream->putByte($b);
                        $new_stream->putByte($a);
                }

                return $new_stream->getBuffer();
        }

        /**
         * @param string $skin
         *
         * @return string|null
         */
        public static function capeToGreyscale(string $skin): ?string{
                $stream = new BinaryStream($skin);

                $new_stream = new BinaryStream();
                $loop = 64 * 32;

                for($i = 0; $i < $loop; $i++){
                        $r = $stream->getByte() & 0xFF;
                        $g = $stream->getByte() & 0xFF;
                        $b = $stream->getByte() & 0xFF;
                        $a = $stream->getByte() & 0xFF;

                        $r = $g = $b = ($r + $g + $b) / 3;

                        $new_stream->putByte($r);
                        $new_stream->putByte($g);
                        $new_stream->putByte($b);
                        $new_stream->putByte($a);
                }

                return $new_stream->getBuffer();
        }
}