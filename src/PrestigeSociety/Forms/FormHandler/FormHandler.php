<?php
namespace PrestigeSociety\Forms\FormHandler;
use pocketmine\player\Player;
use PrestigeSociety\Core\PrestigeSocietyCore;
abstract class FormHandler{
        /** @var PrestigeSocietyCore */
        protected PrestigeSocietyCore $core;

        /** @var mixed */
        protected mixed $data; // used as extra data for forms
        protected int $form_id;

        /** @var array */
        protected array $vars;

        /**
         * FormHandler constructor.
         *
         * @param PrestigeSocietyCore $core
         * @param int                 $formId
         */
        public function __construct(PrestigeSocietyCore $core, int $formId){
                $this->core = $core;
                $this->form_id = $formId;
        }

        /**
         * @return mixed
         */
        public function getData(){
                return $this->data;
        }

        /**
         * @param $data
         */
        public function setData($data): void{
                $this->data = $data;
        }

        /**
         * @param $index
         * @param $value
         */
        public function setVar($index, $value){
                $this->vars[$index] = $value;
        }

        /**
         * @param $index
         *
         * @return mixed
         */
        public function getVar($index): mixed{
                return $this->vars[$index] ?? null;
        }

        /**
         * @param Player $player
         */
        public function handleNull(Player $player){
        }

        /**
         * @param Player $player
         */
        abstract public function send(Player $player);

        /**
         * @param Player $player
         * @param        $formData
         */
        abstract public function handleResponse(Player $player, $formData);
}