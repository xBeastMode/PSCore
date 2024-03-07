<?php
namespace PrestigeSociety\LandProtector\Command;
use Exception;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use PrestigeSociety\Core\Commands\CoreCommand;
use PrestigeSociety\Core\PrestigeSocietyCore;
use PrestigeSociety\Core\Utils\StringUtils;
use PrestigeSociety\Core\Utils\RandomUtils;
use PrestigeSociety\LandProtector\Handle\Sessions;
class LandCommand extends CoreCommand{
        /**
         * LandCommand constructor.
         *
         * @param PrestigeSocietyCore $plugin
         */
        public function __construct(PrestigeSocietyCore $plugin){
                parent::__construct($plugin, "land", "", RandomUtils::colorMessage("&eUsage: /land help"), ["land-protector", "lp"]);
        }

        /**
         * @param CommandSender $sender
         * @param string        $commandLabel
         * @param string[]      $args
         *
         * @return bool
         *
         * @throws Exception
         */
        public function execute(CommandSender $sender, string $commandLabel, array $args): bool{
                if(empty($args[0])){
                        $sender->sendMessage($this->getUsage());
                        return false;
                }
                $mod = $this->core->module_loader->land_protector;
                switch(strtolower($args[0])){
                        case "help":
                                if($this->testCustomPermission($sender, "land.protector.command.help")){
                                        $sender->sendMessage(RandomUtils::colorMessage("&c--=[&d&lPrestigeSociety &fLand &eProtector&r&c]=--"));
                                        $sender->sendMessage(RandomUtils::colorMessage("&e/land select1 : &fsets first position"));
                                        $sender->sendMessage(RandomUtils::colorMessage("&e/land select2 : &fsets second position"));
                                        $sender->sendMessage(RandomUtils::colorMessage("&e/land add <area> : &fadds a protected land from positions"));
                                        $sender->sendMessage(RandomUtils::colorMessage("&e/land mode <area> <damage|edit|touch|burn|explode> <true|false> : &fsets area modes"));
                                        $sender->sendMessage(RandomUtils::colorMessage("&e/land remove <area> : &fremoves specific area"));
                                        $sender->sendMessage(RandomUtils::colorMessage("&e/land list: &flist all the existing areas"));
                                        $sender->sendMessage(RandomUtils::colorMessage("&e/land listhere: &flist all existing areas where you are standing"));
                                        $sender->sendMessage(RandomUtils::colorMessage("&e/land setmine <area> [block-perfecentags...]: &fmake a land a mine"));
                                        $sender->sendMessage(RandomUtils::colorMessage("&e/land removemine <area>: &fremove a land from mine list"));
                                        $sender->sendMessage(RandomUtils::colorMessage("&e/land reset <area>: &freset a land if it's a mine"));
                                        $sender->sendMessage(RandomUtils::colorMessage("&e/land resetempty <area>: &freset a land if it's a mine and is empty"));
                                        $sender->sendMessage(RandomUtils::colorMessage("&e/land resetall : &freset all mines"));
                                        $sender->sendMessage(RandomUtils::colorMessage("&e/land setportal <area> <commands> : &fsets a portal"));
                                        $sender->sendMessage(RandomUtils::colorMessage("&e/land removeportal <area>: &fremoves a portal"));
                                        $sender->sendMessage(RandomUtils::colorMessage("&e/land removeportal <area>: &fremoves a portal"));
                                        $sender->sendMessage(RandomUtils::colorMessage("&e/land restrict <area> <delay> <title...>|<subtitle...>: &frestricts players from portal"));
                                        $sender->sendMessage(RandomUtils::colorMessage("&e/land derestrict <area>: &fderestricts portal"));
                                }
                                break;

                        case "p1":
                        case "select1":
                                if(!$this->testPermissionAndPlayer($sender, "land.protector.command.p1")) return false;
                                Sessions::$selections1[$sender->getName()] = true;
                                $sender->sendMessage(RandomUtils::colorMessage($this->core->getMessage("land_protector", "select_first_position")));
                                break;

                        case "p2":
                        case "select2":
                                if(!$this->testPermissionAndPlayer($sender, "land.protector.command.p2")) return false;
                                Sessions::$selections2[$sender->getName()] = true;
                                $sender->sendMessage(RandomUtils::colorMessage($this->core->getMessage("land_protector", "select_second_position")));
                                break;

                        case "set":
                        case "add":
                                if(!$this->testPermissionAndPlayer($sender, "land.protector.command.add")) return false;

                                /** @var Player $sender */
                                if(isset(Sessions::$setter1[$sender->getName()]) and isset(Sessions::$setter2[$sender->getName()])){
                                        if(!isset($args[1])){
                                                $sender->sendMessage(RandomUtils::colorMessage($this->core->getMessage("land_protector", "missing_land_name")));
                                                return false;
                                        }
                                        if($mod->addArea($args[1], Sessions::$setter1[$sender->getName()], Sessions::$setter2[$sender->getName()], $sender->getWorld())){
                                                $sender->sendMessage(RandomUtils::colorMessage(str_replace("@land", $args[1], $this->core->getMessage("land_protector", "added_land_success"))));
                                                unset(Sessions::$setter1[$sender->getName()]);
                                                unset(Sessions::$setter1[$sender->getName()]);
                                        }else{
                                                $sender->sendMessage(RandomUtils::colorMessage(str_replace("@land", $args[1], $this->core->getMessage("land_protector", "land_exists"))));
                                        }
                                }else{
                                        $sender->sendMessage(RandomUtils::colorMessage($this->core->getMessage("land_protector", "select_positions_first")));
                                }
                                break;

                        case "mode":
                                if(!$this->testCustomPermission($sender, "land.protector.command.mode")) return false;
                                if(!isset($args[1])){
                                        $sender->sendMessage(RandomUtils::colorMessage($this->core->getMessage("land_protector", "missing_land_name")));
                                        return false;
                                }
                                if(!isset($args[2])){
                                        $sender->sendMessage(RandomUtils::colorMessage($this->core->getMessage("land_protector", "missing_mode_name")));
                                        return false;
                                }
                                if(!isset($args[3])){
                                        $sender->sendMessage(RandomUtils::colorMessage($this->core->getMessage("land_protector", "missing_mode_bool")));
                                        return false;
                                }
                                if(RandomUtils::checkBool($args[3])){
                                        if($mod->setMode($args[1], $mod->getModeByName($args[2]), RandomUtils::toBool($args[3]))){
                                                $m = str_replace(["@land", "@mode", "@bool"], [$args[1], $args[2], $args[3]], $this->core->getMessage("land_protector", "mode_change_successful"));
                                                $sender->sendMessage(RandomUtils::colorMessage($m));
                                        }else{
                                                $m = str_replace(["@land", "@mode", "@bool"], [$args[1], $args[2], $args[3]], $this->core->getMessage("land_protector", "invalid_land_name"));
                                                $sender->sendMessage(RandomUtils::colorMessage($m));
                                        }
                                }else{
                                        $m = str_replace(["@land", "@mode", "@bool"], [$args[1], $args[2], $args[3]], $this->core->getMessage("land_protector", "invalid_bool_value"));
                                        $sender->sendMessage(RandomUtils::colorMessage($m));
                                }
                                break;

                        case "remove":
                                if(!$this->testCustomPermission($sender, "land.protector.command.remove")) return false;
                                if(!isset($args[1])){
                                        $sender->sendMessage(RandomUtils::colorMessage($this->core->getMessage("land_protector", "missing_land_name")));
                                        return false;
                                }
                                if($mod->removeArea($args[1])){
                                        $this->core->module_loader->mine_resetter->unloadMine($args[1]);
                                        $sender->sendMessage(RandomUtils::colorMessage(str_replace("@land", $args[1], $this->core->getMessage("land_protector", "removed_land_success"))));
                                }else{
                                        $sender->sendMessage(RandomUtils::colorMessage(str_replace("@land", $args[1], $this->core->getMessage("land_protector", "land_does_not_exist"))));
                                }
                                break;

                        case "list":
                                if(!$this->testCustomPermission($sender, "land.protector.command.list")) return false;

                                $areas = $mod->getAllAreasNames();
                                $areas = implode(", ", $areas);
                                $len = strlen($areas);
                                $message = $this->core->getMessage("land_protector", "land_list");
                                $message = $len > 0 ? str_replace("@list", $areas, $message) : str_replace("@list", "none", $message);
                                $sender->sendMessage(RandomUtils::colorMessage($message));

                                break;

                        case "listhere":
                                if(!$this->testPermissionAndPlayer($sender, "land.protector.command.listhere")) return false;

                                /** @var Player $sender */

                                $areas = $mod->getAreasByVector($sender->getLocation()->asVector3(), $sender->getWorld());
                                $areas = implode(", ", $areas);
                                $len = strlen($areas);
                                $message = $this->core->getMessage("land_protector", "land_list_here");
                                $message = $len > 0 ? str_replace("@list", $areas, $message) : str_replace("@list", "none", $message);
                                $sender->sendMessage(RandomUtils::colorMessage($message));

                                break;

                        case "setmine":
                                if(!$this->testCustomPermission($sender, "land.protector.command.setmine")) return false;
                                if(count($args) < 3){
                                        $sender->sendMessage(RandomUtils::colorMessage("&cUsage is: /land setmine <area> <block-perfecentags...>"));
                                        return false;
                                }

                                array_shift($args);

                                $area = array_shift($args);

                                /** @var \DateTime $time */
                                //$time = $out[0];
                                $blocks = $args;

                                $i = 1;

                                if(!empty($args)){

                                        $keys = [];
                                        $values = [];

                                        $total = 0;

                                        foreach($blocks as $value){
                                                if(($i % 2) === 0){
                                                        $last = substr($value, -1);
                                                        $actual = str_replace("%", "", $value);
                                                        if($last === "%" && StringUtils::checkIsNumber($actual)){
                                                                $total += intval($actual);
                                                                $values[] = intval($actual);
                                                        }elseif($last !== "%" || !StringUtils::checkIsNumber($actual)){
                                                                $sender->sendMessage(RandomUtils::colorMessage("&cError: argument " . $i . " (" . $value . ") does not specify a percentage."));
                                                                return false;
                                                        }
                                                }else{
                                                        $actual = str_replace("%", "", $value);
                                                        $actual = explode(":", $actual);
                                                        if(count($actual) > 1){
                                                                if(StringUtils::checkIsNumber($actual[0]) && StringUtils::checkIsNumber($actual[1])){
                                                                        $keys[] = implode(":", $actual);
                                                                }else{
                                                                        $sender->sendMessage(RandomUtils::colorMessage("&cError: argument " . $i . " (" . $value . ") does not specify a block id or meta."));
                                                                }
                                                        }else{
                                                                $actual = implode("", $actual);
                                                                if(StringUtils::checkIsNumber($actual)){
                                                                        $keys[] = $actual;
                                                                }else{
                                                                        $sender->sendMessage(RandomUtils::colorMessage("&cError: argument " . $i . " (" . $value . ") does not specify a block id or meta."));
                                                                }
                                                        }
                                                }
                                                ++$i;
                                        }

                                        if($total < 100){
                                                $sender->sendMessage(RandomUtils::colorMessage("&cError: Given percentage does not add up to 100. You gave: " . $total . "%"));
                                                return false;
                                        }elseif($total > 100){
                                                $sender->sendMessage(RandomUtils::colorMessage("&cError: Given percentage is more than 100. You gave: " . $total . "%"));
                                                return false;
                                        }

                                        $outputBlocks = array_combine($keys, $values);
                                }else{
                                        $outputBlocks = ["1:0" => "50", "14:0" => "25", "56:0" => "25"];
                                }

                                //$resetTime = ($time->getTimestamp() - time());

                                if($mod->setExtraData($area, ["mine" => 1, "blocks" => $outputBlocks/*, "reset_time" => ($resetTime > 0 ? $resetTime : 30 * 60)*/])){
                                        $this->core->module_loader->mine_resetter->loadMine($area);
                                        $sender->sendMessage(RandomUtils::colorMessage(str_replace("@land", $area, $this->core->getMessage("mines", "set_mine_success"))));
                                }else{
                                        $sender->sendMessage(RandomUtils::colorMessage(str_replace("@land", $area, $this->core->getMessage("mines", "land_does_not_exist"))));
                                }
                                break;
                        case "removemine":
                                if(!$this->testCustomPermission($sender, "land.protector.command.removemine")) return false;

                                if(!isset($args[1])){
                                        $sender->sendMessage(RandomUtils::colorMessage($this->core->getMessage("land_protector", "missing_land_name")));
                                        return false;
                                }

                                $area = $args[1];

                                if($mod->removeExtraData($area, ["mine", "blocks", "reset_time"])){
                                        $this->core->module_loader->mine_resetter->unloadMine($area);
                                        $sender->sendMessage(RandomUtils::colorMessage($this->core->getMessage("mines", "removed_mine")));
                                }else{
                                        $sender->sendMessage(RandomUtils::colorMessage(str_replace("@mine", $area, $this->core->getMessage("mines", "mine_does_not_exist"))));
                                }
                                break;
                        case "reset":
                        case "resetmine":
                                if(!$this->testCustomPermission($sender, "land.protector.command.resetmine")) return false;

                                if(!isset($args[1])){
                                        $sender->sendMessage(RandomUtils::colorMessage($this->core->getMessage("land_protector", "missing_land_name")));
                                        return false;
                                }

                                $area = $args[1];

                                if(!$this->core->module_loader->mine_resetter->notifyRestart($area)){
                                        $sender->sendMessage(RandomUtils::colorMessage(str_replace("@mine", $area, $this->core->getMessage("mines", "mine_does_not_exist"))));
                                }

                                break;
                        case "resetempty":
                        case "resetemptymine":
                                if(!isset($args[1])){
                                        $sender->sendMessage(RandomUtils::colorMessage($this->core->getMessage("land_protector", "missing_land_name")));
                                        return false;
                                }

                                $area = $args[1];
                                $name = $sender->getName();
                                $exists = $this->core->module_loader->mine_resetter->resetEmptyMines($area, function ($area, $empty) use ($name){
                                        $core = PrestigeSocietyCore::getInstance();
                                        if($empty){
                                                $core->module_loader->mine_resetter->notifyRestart($area);
                                        }else{
                                                $message = RandomUtils::colorMessage(str_replace("@mine", $area, $core->getMessage("mines", "mine_not_empty")));
                                                if($name === "CONSOLE"){
                                                        $core->getLogger()->info($message);
                                                }elseif(($player = $core->getServer()->getPlayerByPrefix($name)) !== null){
                                                        $player->sendMessage($message);
                                                }
                                        }
                                });

                                if(!$exists){
                                        $sender->sendMessage(RandomUtils::colorMessage(str_replace("@mine", $area, $this->core->getMessage("mines", "mine_does_not_exist"))));
                                }

                                break;
                        case "resetall":
                                if(!$this->testCustomPermission($sender, "land.protector.command.resetmine.all")) return false;
                                $this->core->module_loader->mine_resetter->notifyRestart();
                                break;
                        case "setportal":
                                if(!$this->testCustomPermission($sender, "land.protector.command.setportal")) return false;
                                if(count($args) < 3){
                                        $sender->sendMessage(RandomUtils::colorMessage("&cUsage is: /land setportal <area> <commands...>"));
                                        return false;
                                }

                                array_shift($args);
                                $area = array_shift($args);

                                $output = ["c" => [], "p" => []];

                                $commands = implode(" ", $args);
                                $commands = explode(";", $commands);

                                foreach($commands as $command){
                                        $parts = explode(":", $command);
                                        if(count($parts) < 2) continue;
                                        switch($parts[0]){
                                                case "p":
                                                        $output["p"][] = $parts[1];
                                                        break;
                                                case "c":
                                                        $output["c"][] = $parts[1];
                                                        break;
                                        }
                                }

                                if($mod->setExtraData($area, ["portal" => 1, "commands" => $output])){
                                        $this->core->module_loader->portals->loadPortal($area);
                                        $sender->sendMessage(RandomUtils::colorMessage(str_replace("@portal", $area, $this->core->getMessage("portals", "set_portal_success"))));
                                }else{
                                        $sender->sendMessage(RandomUtils::colorMessage(str_replace("@portal", $area, $this->core->getMessage("portals", "land_does_not_exist"))));
                                }
                                break;
                        case "removeportal":
                                if(!$this->testCustomPermission($sender, "land.protector.command.removeportal")) return false;
                                if(!isset($args[1])){
                                        $sender->sendMessage(RandomUtils::colorMessage($this->core->getMessage("portals", "missing_land_name")));
                                        return false;
                                }

                                $area = $args[1];

                                if($mod->removeExtraData($area, ["portal", "commands"])){
                                        $this->core->module_loader->portals->unloadPortal($area);
                                        $sender->sendMessage(RandomUtils::colorMessage($this->core->getMessage("portals", "removed_portal")));
                                }else{
                                        $sender->sendMessage(RandomUtils::colorMessage(str_replace("@portal", $area, $this->core->getMessage("portals", "portal_does_not_exist"))));
                                }
                                break;
                        case "restrict":
                                if(!$this->testCustomPermission($sender, "land.protector.command.restrict")) return false;
                                if(count($args) < 5){
                                        $sender->sendMessage(RandomUtils::colorMessage("&cUsage is: /land restrict <area> <delay> <title...>|<subtitle...>"));
                                        return false;
                                }

                                array_shift($args);
                                $area = array_shift($args);
                                $delay = array_shift($args);

                                if(!is_numeric($delay)) $delay = 200;

                                list($title, $subtitle) = explode("|", implode(" ", $args));

                                if($mod->setRestricted($area, $delay, $title, $subtitle)){
                                        $this->core->module_loader->portals->reloadPortals();
                                        $sender->sendMessage(RandomUtils::colorMessage(str_replace("@portal", $area, $this->core->getMessage("portals", "set_restricted_success"))));
                                }else{
                                        $sender->sendMessage(RandomUtils::colorMessage(str_replace("@portal", $area, $this->core->getMessage("portals", "land_does_not_exist"))));
                                }
                                break;
                        case "derestrict":
                                if(!$this->testCustomPermission($sender, "land.protector.command.derestrict")) return false;
                                if(count($args) < 2){
                                        $sender->sendMessage(RandomUtils::colorMessage("&cUsage is: /land derestrict <area>"));
                                        return false;
                                }

                                array_shift($args);
                                $area = array_shift($args);

                                if($mod->removeRestricted($area)){
                                        $this->core->module_loader->portals->reloadPortals();
                                        $sender->sendMessage(RandomUtils::colorMessage(str_replace("@portal", $area, $this->core->getMessage("portals", "unset_restricted_success"))));
                                }else{
                                        $sender->sendMessage(RandomUtils::colorMessage(str_replace("@portal", $area, $this->core->getMessage("portals", "land_does_not_exist"))));
                                }
                                break;
                }
                return true;
        }
}