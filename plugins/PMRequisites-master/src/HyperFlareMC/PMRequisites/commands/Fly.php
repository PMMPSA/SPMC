<?php

declare(strict_types=1);

namespace HyperFlareMC\PMRequisites\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class Fly extends Command{

    public function __construct(){
        parent::__construct(
            "fly",
            "Enable flight mode!",
            TF::RED . "Usage: " . TF::GRAY . "/fly"
        );
        $this->setPermission("pmrequisites.fly");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(!$sender->hasPermission("pmrequisites.fly")){
            $sender->sendMessage(TF::RED . "You do not have permission to use this command!");
            return;
        }
        if(!$sender instanceof Player){
            $sender->sendMessage(TF::RED . "You must be in-game to use this command!");
            return;
        }
        if(count($args) !== 0){
            $sender->sendMessage($this->usageMessage);
            return;
        }
        if($sender->isSurvival() or $sender->isAdventure()){
            if($sender->getAllowFlight()){
                $sender->setAllowFlight(false);
                $sender->sendMessage(TF::GREEN . "Flight mode disabled!");
            }else{
                $sender->setAllowFlight(true);
                $sender->setFlying(true);
                $sender->sendMessage(TF::GREEN . "Flight mode enabled!");
            }
        }else{
            $sender->sendMessage(TF::RED . "You must be in Survival or Adventure mode to use this command!");
            return;
        }
    }

}