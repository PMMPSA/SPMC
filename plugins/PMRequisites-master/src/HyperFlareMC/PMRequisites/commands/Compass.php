<?php

declare(strict_types=1);

namespace HyperFlareMC\PMRequisites\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class Compass extends Command{

    public function __construct(){
        parent::__construct(
            "compass",
            "Check your directional position",
            TF::RED . "Usage: " . TF::GRAY . "/compass"
        );
        $this->setPermission("pmrequisites.compass");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(!$sender->hasPermission("pmrequisites.compass")){
            $sender->sendMessage(TF::RED . "You do not have permission to use this command");
            return;
        }
        if(!$sender instanceof Player){
            $sender->sendMessage(TF::RED . "You must be in-game to use this command!");
            return;
        }
        if(count($args) > 1){
            $sender->sendMessage($this->usageMessage);
            return;
        }
        switch($sender->getDirection()){
            case 0:
                $direction = "south";
                break;
            case 1:
                $direction = "west";
                break;
            case 2:
                $direction = "north";
                break;
            case 3:
                $direction = "east";
                break;
            default:
                $sender->sendMessage(TF::RED . "There was an error getting your direction!");
                return;
                break;
        }
        $sender->sendMessage(TF::GREEN . "You're facing " . TF::YELLOW . strtoupper($direction));
    }

}