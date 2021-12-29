<?php

declare(strict_types=1);

namespace HyperFlareMC\PMRequisites\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat as TF;

class Heal extends Command{

    public function __construct(){
        parent::__construct(
            "heal",
            "Heal yourself or another player!",
            TF::RED . "Usage: " . TF::GRAY . "/heal or /heal <player>"
        );
        $this->setPermission("pmrequisites.heal");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        $server = Server::getInstance();
        if(!$sender->hasPermission("pmrequisites.heal")){
            $sender->sendMessage(TF::RED . "You do not have permission to use this command!");
            return;
        }
        if(!$sender instanceof Player && count($args) === 0){
            $sender->sendMessage(TF::RED . "You must be in-game to heal yourself!");
            return;
        }
        if(!isset($args[0])){
            $sender->setHealth(20);
            $sender->sendMessage(TF::GREEN . "You have been healed!");
        }else{
            $target = $server->getPlayer($args[0]);
            if($target === null){
                $sender->sendMessage(TF::RED . "Player not found!");
                return;
            }
            $target->setHealth(20);
            $target->sendMessage(TF::GREEN . "You have been healed by " . TF::YELLOW . $sender->getName() . TF::GREEN . "!");
            $sender->sendMessage(TF::GREEN . "You have healed " . TF::YELLOW . $target->getName() . TF::GREEN . "!");
        }
    }
}