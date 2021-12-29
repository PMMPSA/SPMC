<?php

declare(strict_types=1);

namespace HyperFlareMC\PMRequisites\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat as TF;

class GetPos extends Command{

    public function __construct(){
        parent::__construct(
            "getpos",
            "Get your or another's XYZ position!",
            TF::RED . "Usage: " . TF::GRAY . "/getpos or /getpos <player>"
        );
        $this->setPermission("pmrequisites.getpos");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        $server = Server::getInstance();
        if(!$sender->hasPermission("pmrequisites.getpos")){
            $sender->sendMessage(TF::RED . "You do not have permission to use this command!");
            return;
        }
        if(!$sender instanceof Player && count($args) === 0){
            $sender->sendMessage(TF::RED . "You must be in-game to check your own XYZ position");
            return;
        }
        if(!isset($args[0])){
            $x = $sender->getFloorX();
            $y = $sender->getFloorY();
            $z = $sender->getFloorZ();
            $sender->sendMessage(TF::GREEN . "Your position: " . TF::YELLOW . $x . ", " . $y . ", " . $z);
        }else{
            $target = $server->getPlayer($args[0]);
            if($target === null){
                $sender->sendMessage(TF::RED . "Player not found!");
                return;
            }
            $x = $target->getFloorX();
            $y = $target->getFloorY();
            $z = $target->getFloorZ();
            $sender->sendMessage(TF::YELLOW . $target->getName() . TF::GREEN . "'s position is: " . TF::YELLOW . $x . ", " . $y . ", " . $z);
        }
    }

}