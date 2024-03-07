<?php
namespace PrestigeSociety\Nicknames;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\DataModels\NicksModel;
class Nicknames{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /**
         * Nicknames constructor.
         *
         * @param PrestigeSocietyCore $core
         */
        public function __construct(PrestigeSocietyCore $core){
                $this->core = $core;
        }

        /**
         * @param $player
         *
         * @return bool
         */
        public function hasNick($player): bool{
                return NicksModel::query()->where('original', '=', RandomUtils::getName($player))->exists();
        }

        /**
         * @param        $player
         * @param string $nick
         */
        public function setNick($player, string $nick){
                NicksModel::query()->updateOrCreate(['original' => RandomUtils::getName($player), 'nick' => $nick]);
        }

        /**
         * @param $player
         *
         * @return string|null
         */
        public function getNick($player): ?string{
                $record = NicksModel::query()->where('original', '=', RandomUtils::getName($player));
                return $record->exists() ? $record->value('nick') : null;
        }

        /**
         * @param $player
         *
         * @return bool
         */
        public function resetNick($player): bool{
                $record = NicksModel::query()->where('original', '=', RandomUtils::getName($player));
                if($record->exists()){
                        $record->delete();
                        return true;
                }
                return false;
        }
}