<?php
namespace PrestigeSociety\Nicknames;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\DataModels\NicksModel;
class StaticNicknames{
        /**
         * @param $player
         *
         * @return bool
         */
        public static function hasNick($player): bool{
                return NicksModel::query()->where('original', '=', RandomUtils::getName($player))->exists();
        }

        /**
         * @param        $player
         * @param string $nick
         */
        public static function setNick($player, string $nick){
                NicksModel::query()->updateOrCreate(['original' => RandomUtils::getName($player), 'nick' => $nick]);
        }

        /**
         * @param $player
         *
         * @return string|null
         */
        public static function getNick($player): ?string{
                $record = NicksModel::query()->where('original', '=', RandomUtils::getName($player));
                return $record->exists() ? $record->value('nick') : null;
        }

        /**
         * @param $player
         *
         * @return bool
         */
        public static function resetNick($player): bool{
                $record = NicksModel::query()->where('original', '=', RandomUtils::getName($player));
                if($record->exists()){
                        $record->delete();
                        return true;
                }
                return false;
        }
}