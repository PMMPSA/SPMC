<?php

declare(strict_types=1);

namespace HyperFlareMC\PMRequisites\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\level\Location;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat as TF;

class Spawn extends Command{

    public function __construct(){
        parent::__construct(
            "spawn",
            "Teleport to the server spawnpoint!",
            TF::RED . "Usage: " . TF::GRAY . "/spawn"
        );
        $this->setPermission("pmrequisites.spawn");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        $server = Server::getInstance();
        if(!$sender->hasPermission("pmrequisites.spawn")){
            $sender->sendMessage(TF::RED . "You do not have permission to use this command!");
            return;
        }
        if(!$sender instanceof Player){
            $sender->sendMessage(TF::RED . "You must be in-game to use this command!");
            return;
        }
        if(count($args) > 0){
            $sender->sendMessage($this->usageMessage);
            return;
        }
        $sender->teleport(Location::fromObject($server->getDefaultLevel()->getSpawnLocation(), $server->getDefaultLevel()));
    }

}