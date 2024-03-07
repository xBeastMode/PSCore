<?php

declare(strict_types=1);

namespace PrestigeSociety\UIForms;
use pocketmine\form\Form;
use pocketmine\form\FormValidationException;
use pocketmine\player\Player;
use PrestigeSociety\Forms\FormHandler\FormHandler;
class CustomForm implements Form{
        /** @var array */
        protected array $form_data = [];

        /** @var FormHandler */
        protected FormHandler $callable;

        /**
         * CustomForm constructor.
         *
         * @param FormHandler $callable $callable
         */
        public function __construct(FormHandler $callable){
                $this->form_data["type"] = "custom_form";
                $this->form_data["content"] = [];

                $this->callable = $callable;
        }

        /**
         * @param array $form_data
         */
        public function setFormData(array $form_data): void{
                $this->form_data = $form_data;
        }

        /**
         * @return array
         */
        public function getFormData(): array{
                return $this->form_data;
        }

        /**
         * @return string
         */
        public function getEncodedFormData(): string{
                return json_encode($this->form_data);
        }

        /**
         * @param Player $player
         */
        public function send(Player $player) {
                $player->sendForm($this);
        }

        /**
         * @param string $title
         */
        public function setTitle(string $title) {
                $this->form_data["title"] = $title;
        }

        /**
         * @param string $label
         */
        public function setLabel(string $label) {
                $this->form_data["content"][] = [
                    "type" => "label",
                    "text" => $label,
                ];
        }

        /**
         * @param string    $toggle
         * @param bool|null $value
         */
        public function setToggle(string $toggle, bool $value = null){
                $this->form_data["content"][] = [
                    "type" => "toggle",
                    "text" => $toggle,
                    "default" => $value !== null ? $value : false
                ];
        }

        /**
         * @param string   $slider
         * @param int      $min
         * @param int      $max
         * @param int|null $step
         * @param int|null $default
         */
        public function setSlider(string $slider, int $min, int $max, int $step = null, int $default = null){
                $this->form_data["content"][] = [
                    "type" => "slider",
                    "text" => $slider,
                    "min" => $min,
                    "max" => $max,
                    "step" => $step !== null ? $step : 1,
                    "default" => $default !== null ? $default : 1
                ];
        }

        /**
         * @param string   $dropdown
         * @param array    $options
         * @param int|null $default
         */
        public function setDropdown(string $dropdown, array $options, int $default = null){
                $this->form_data["content"][] = [
                    "type" => "dropdown",
                    "text" => $dropdown,
                    "options" => $options,
                    "default" => $default !== null ? $default : 1
                ];
        }

        /**
         * @param string      $input
         * @param string      $placeholder
         * @param string|null $default
         */
        public function setInput(string $input, string $placeholder = "", string $default = null){
                $this->form_data["content"][] = [
                    "type" => "input",
                    "text" => $input,
                    "placeholder" => $placeholder,
                    "default" => $default !== null ? $default : ""
                ];
        }

        /**
         * Handles a form response from a player.
         *
         * @param Player $player
         * @param mixed  $data
         *
         * @throws FormValidationException if the data could not be processed
         */
        public function handleResponse(Player $player, $data): void{
                if($data !== null){
                        $this->callable->handleResponse($player, $data);
                }else{
                        $this->callable->handleNull($player);
                }
        }

        /**
         * Specify data which should be serialized to JSON
         * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
         *
         * @return mixed data which can be serialized by <b>json_encode</b>,
         * which is a value of any type other than a resource.
         *
         * @since 5.4.0
         */
        public function jsonSerialize(): mixed{
                return $this->form_data;
        }
}