<?php

declare(strict_types=1);

namespace HyperFlareMC\PMRequisites\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class SetSpawn extends Command{

    public function __construct(){
        parent::__construct(
            "setspawn",
            "Set the server's main spawnpoint!",
            TF::RED . "Usage: " . TF::GRAY . "/setspawn"
        );
        $this->setPermission("pmrequisites.setspawn");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(!$sender->hasPermission("pmrequisites.setspawn")){
            $sender->sendMessage(TF::RED . "You do not have permission to use this command!");
            return;
        }
        if(!$sender instanceof Player){
            $sender->sendMessage(TF::RED . "You do not have permission to use this command!");
            return;
        }
        if(count($args) > 0){
            $sender->sendMessage($this->usageMessage);
            return;
        }
        $sender->getLevel()->setSpawnLocation($sender);
        $sender->getServer()->setDefaultLevel($sender->getLevel());
        $x = $sender->getFloorX();
        $y = $sender->getFloorY();
        $z = $sender->getFloorZ();
        $sender->sendMessage(TF::GREEN . "Server's spawn point set to: " . TF::YELLOW . $x . ", " . $y . ", " . $z);
    }

}