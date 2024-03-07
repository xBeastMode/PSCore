<?php
namespace PrestigeSociety\Core\Utils;
use JetBrains\PhpStorm\Pure;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
class StringUtils{
        const CHARS = [
            "=", "&", "<", ">", "/", "$", "#", "!", "-", "_", "+", ".", "@",
            "(", ")", "*", "^", "%", ";", ":", "?", "[", "]", "{", "}", "~"
        ];

        private static int $last_color = 0;

        /**
         * @param string     $string
         * @param array|null $elements
         *
         * @return array|string
         */
        public static function _(string $string, array $elements = null): array|string{
                for($i = 0; $i < strlen($string) + 1; ++$i){
                        if(stripos("%$i", $string) !== false and isset($elements[$i - 1])){
                                $string = str_replace("%$i", $elements[$i - 1], $string);
                        }
                }
                return str_replace("%%", "\xc2\xa7", $string);
        }

        /**
         * @param float  $value
         * @param string $exploder
         *
         * @return string
         */
        public static function double(float $value, string $exploder = "."): string{
                return number_format((float)$value, 2, $exploder, "");
        }

        /**
         * @param $value
         *
         * @return bool
         */
        public static function checkIsNumber($value): bool{
                return is_numeric($value) || is_int($value) || is_float($value);
        }

        /**
         * @param string $value
         *
         * @return int
         */
        #[Pure] public static function stringToInteger(string $value): int{
                return self::checkIsNumber($value) ? intval($value) : 0;
        }

        /**
         * @param array $values
         *
         * @return null|bool
         */
        public static function randomValue(array $values): ?bool{
                return empty($values) ? null : $values[array_rand($values)];
        }

        /**
         * @param int $length
         *
         * @return int
         */
        #[Pure] public static function randomNumber(int $length): int{
                $num = range(0, 9);
                $n = str_repeat(StringUtils::randomValue($num), $length);
                return intval($n);
        }

        /**
         * @param int        $length
         * @param bool|true  $numbers
         * @param bool|false $chars
         *
         * @return string
         */
        #[Pure] public static function randomString(int $length, bool $numbers = true, bool $chars = false): string{
                $alphabet = range('A', 'Z');
                $number_range = range(0, 9);
                $output_string = "";
                if($numbers){
                        for($i = 0; $i < $length / 2; ++$i){
                                $output_string .= StringUtils::randomValue($alphabet);
                                $output_string .= StringUtils::randomValue($number_range);
                        }
                }
                if($chars){
                        for($i = 0; $i < $length / 2; ++$i){
                                $output_string .= StringUtils::randomValue($alphabet);
                                $output_string .= StringUtils::randomValue(StringUtils::CHARS);
                        }
                }
                return $output_string;
        }

        /**
         * @param string     $string
         * @param bool|false $numbers
         * @param bool|false $chars
         *
         * @return string
         */
        #[Pure] public static function mixString(string $string, bool $numbers = false, bool $chars = false): string{
                $number = range(0, 9);
                $output_string = "";
                if($numbers){
                        for($i = 0; $i < strlen($string); ++$i){
                                $output_string .= $string[$i];
                                $output_string .= StringUtils::randomValue($number);
                        }
                }
                if($chars){
                        for($i = 0; $i < strlen($string); ++$i){
                                $output_string .= $string[$i];
                                $output_string .= StringUtils::randomValue(StringUtils::CHARS);
                        }
                }
                return $output_string;
        }

        /**
         * @param string $string
         * @param int    $quantitative
         *
         * @return int
         */
        #[Pure] public static function parseStringEquation(string $string, int $quantitative): int{
                $char = substr($string, 0, 1);
                $number = self::stringToInteger(substr($string, 1));

                switch($char){
                        case "-":
                                return $quantitative - $number;
                        case "+":
                                return $quantitative + $number;
                        case "/":
                                return $number > 0 ? $quantitative / $number : $quantitative;
                        case "*":
                                return $number > 0 ? $quantitative * $number : $quantitative;
                }

                return StringUtils::stringToInteger($string);
        }

        /**
         * @param string $string
         *
         * @return array
         */
        public static function getChars(string $string): array{
                preg_match_all("/[[:punct:]]/", $string, $matches);
                return $matches[0];
        }

        /**
         * @param string $string
         *
         * @return string
         */
        public static function replaceChars(string $string): string{
                foreach(StringUtils::getChars($string) as $char){
                        $string = str_replace($char, "", $string);
                }
                return $string;
        }

        /**
         * @param string $string
         *
         * @return string
         */
        public static function replaceAllKeepLetters(string $string): string{
                return preg_replace("/[^A-Za-z]/", "", $string);
        }

        /**
         * @param $text
         *
         * @return string
         */
        public static function cleanString($text): string{
                $utf8 = array(
                    '/[áàâãªä]/u'   =>   'a',
                    '/[ÁÀÂÃÄ]/u'    =>   'A',
                    '/[ÍÌÎÏ]/u'     =>   'I',
                    '/[íìîï]/u'     =>   'i',
                    '/[éèêë]/u'     =>   'e',
                    '/[ÉÈÊË]/u'     =>   'E',
                    '/[óòôõºö]/u'   =>   'o',
                    '/[ÓÒÔÕÖ]/u'    =>   'O',
                    '/[úùûü]/u'     =>   'u',
                    '/[ÚÙÛÜ]/u'     =>   'U',
                    '/ç/'           =>   'c',
                    '/Ç/'           =>   'C',
                    '/ñ/'           =>   'n',
                    '/Ñ/'           =>   'N',
                    '/–/'           =>   '-',
                    '/[’‘‹›‚]/u'    =>   ' ',
                    '/[“”«»„]/u'    =>   ' ',
                    '/ /'           =>   ' ',
                );
                return preg_replace(array_keys($utf8), array_values($utf8), $text);
        }

        /**
         * @return string
         */
        public static function randomColor(): string{
                //$colors = ["&1", "&2", "&3", "&4", "&5", "&6", "&9", "&a", "&b", "&c", "&d", "&e"];
                $colors = ["&a", "&b", "&c", "&d", "&e", "&g", "&f", "&6"];

                self::$last_color++;
                if(self::$last_color > count($colors) - 1){
                        self::$last_color = 0;
                }

                return $colors[self::$last_color];
        }

        /**
         * @param $string
         *
         * @return string
         */
        public static function clearColors($string): string{
                $colors = ["&a", "&b", "&c", "&d", "&e", "&f", "&r", "&k", "&l", "&o"];
                for($i = 0; $i < 10; ++$i){
                        $string = str_replace("&$i", "", $string);
                }
                foreach($colors as $c){
                        $string = str_replace($c, "", $string);
                }
                return $string;
        }

        /**
         * @param array $values
         *
         * @return array
         */
        public static function returnArrayOfMultidimensionalArray(array $values): array{
                $result = [];
                $values = new RecursiveIteratorIterator(new RecursiveArrayIterator($values));
                foreach($values as $value){
                        $result[] = $value;
                }
                return $result;
        }

        /**
         * @param string $seconds
         *
         * @return int[]
         */
        public static function secondsToDHMS(string $seconds): array{
                $daytimeNow = new \DateTime('@0');
                $daytimeSeconds = new \DateTime("@$seconds");

                $daytimeDiff = $daytimeNow->diff($daytimeSeconds);
                return [$daytimeDiff->days, $daytimeDiff->h, $daytimeDiff->i, $daytimeDiff->s];
        }

        /**
         * @param string $string
         *
         * @return array|null
         *
         * @throws \Exception
         */
        public static function stringToTimestamp(string $string): ?array{
                if(trim($string) === ""){
                        return null;
                }
                $time = new \DateTime();
                preg_match_all("/[0-9]+(y|mo|w|d|h|m|s)|[0-9]+/", $string, $found);
                if(count($found[0]) < 1){
                        return null;
                }
                $found[2] = preg_replace("/[^0-9]/", "", $found[0]);
                foreach($found[2] as $k => $i){
                        switch($c = $found[1][$k]){
                                case "y":
                                case "w":
                                case "d":
                                        $time->add(new \DateInterval("P" . $i . strtoupper($c)));
                                        break;
                                case "mo":
                                        $time->add(new \DateInterval("P" . $i . strtoupper(substr($c, 0, strlen($c) - 1))));
                                        break;
                                case "h":
                                case "m":
                                case "s":
                                        $time->add(new \DateInterval("PT" . $i . strtoupper($c)));
                                        break;
                                default:
                                        $time->add(new \DateInterval("PT" . $i . "S"));
                                        break;
                        }
                        $string = str_replace($found[0][$k], "", $string);
                }
                return [$time, ltrim(str_replace($found[0], "", $string))];
        }
}