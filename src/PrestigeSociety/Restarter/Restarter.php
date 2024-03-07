<?php
namespace PrestigeSociety\Restarter;
use JetBrains\PhpStorm\Pure;
class Restarter{

        /** @var int */
        public int $time = 0;

        /**
         * Restarter constructor.
         *
         * @param int $time
         */
        public function __construct(int $time){
                $this->time = $time;
        }

        /**
         * @return string
         */
        #[Pure] public function __toString(): string{
                return ($this->toHours() . ":" . $this->toMinutes() . ":" . $this->toSeconds());
        }

        /**
         * @param int $time
         */
        public function setTime(int $time): void{
                $this->time = $time;
        }

        /**
         * @param int $time
         */
        public function addTime(int $time): void{
                $this->time += $time;
        }

        /**
         * @param int $time
         */
        public function subtractTime(int $time): void{
                $this->time -= $time;
        }

        /**
         * @param int $time
         */
        public function divideTime(int $time): void{
                $this->time /= $time;
        }

        /**
         * @param int $time
         */
        public function multiplyTime(int $time): void{
                $this->time *= $time;
        }

        /**
         * @return int
         */
        public function getTime(): int{
                return $this->time;
        }

        /**
         * @return float
         */
        public function toHours(): float{
                return floor($this->time / 3600);
        }

        /**
         * @return float
         */
        public function toMinutes(): float{
                return floor(($this->time / 60) - (floor($this->time / 3600) * 60));
        }

        /**
         * @return float
         */
        public function toSeconds(): float{
                return floor($this->time % 60);
        }
}