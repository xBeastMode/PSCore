<?php
namespace PrestigeSociety\Core\Utils;
class TextureUtils{
        /**
         * @param string $file
         * @param int    $size
         *
         * @return null|string
         */
        public static function getTexture(string $file, int $size = 64): ?string{
                return static::textureFromPNGFile($file, $size);
        }

        /**
         * @param string $file
         * @return bool|string
         */
        public static function getGeometryData(string $file): bool|string{
                return file_get_contents($file);
        }

        /**
         * @param string $path
         * @param int    $size
         *
         * @return null|string
         */
        public static function textureFromPNGFile(string $path, int $size = 64): ?string{
                $img = @imagecreatefrompng($path);
                $height = (int) @getimagesize($path)[1];

                $texture_bytes = "";

                for ($y = 0; $y < $height; $y++) {
                        for ($x = 0; $x < $size; $x++) {
                                $argb = @imagecolorat($img, $x, $y);
                                $a = ((~((int)($argb >> 24))) << 1) & 0xff;
                                $r = ($argb >> 16) & 0xff;
                                $g = ($argb >> 8) & 0xff;
                                $b = $argb & 0xff;
                                $texture_bytes .= chr($r) . chr($g) . chr($b) . chr($a);
                        }
                }

                @imagedestroy($img);
                return $texture_bytes;
        }
}