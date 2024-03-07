<?php
namespace PrestigeSociety\Events\Events\Boss;
use pocketmine\player\Player;
use PrestigeSociety\Bosses\Entity\BossEntity;
use PrestigeSociety\Events\CoreEvent;
class BossDeathEvent extends CoreEvent{
        /** @var BossEntity */
        protected BossEntity $boss_entity;
        /** @var Player */
        protected Player $killer;
        /** @var Player[] */
        protected array $participants;

        /**
         * BossDeathEvent constructor.
         *
         * @param BossEntity $boss_entity
         * @param Player     $killer
         * @param array      $participants
         */
        public function __construct(BossEntity $boss_entity, Player $killer, array $participants){
                $this->boss_entity = $boss_entity;
                $this->killer = $killer;
                $this->participants = $participants;
        }

        /**
         * @return BossEntity
         */
        public function getBossEntity(): BossEntity{
                return $this->boss_entity;
        }

        /**
         * @return Player
         */
        public function getKiller(): Player{
                return $this->killer;
        }

        /**
         * @return Player[]
         */
        public function getParticipants(): array{
                return $this->participants;
        }
}