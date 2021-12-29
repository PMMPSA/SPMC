<?php

declare(strict_types=1);

namespace HyperFlareMC\PMRequisites\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat as TF;

class Feed extends Command{

    public function __construct(){
        parent::__construct(
            "feed",
            "Feed yourself or another player!",
            TF::RED . "Usage: " . TF::GRAY . "/feed or /feed <player>"
        );
        $this->setPermission("pmrequisites.feed");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        $server = Server::getInstance();
        if(!$sender->hasPermission("pmrequisites.feed")){
            $sender->sendMessage(TF::RED . "You do not have permission to use this command!");
            return;
        }
        if(!$sender instanceof Player && count($args) === 0){
            $sender->sendMessage(TF::RED . "You must be in-game to feed yourself!");
            return;
        }
        if(!isset($args[0])){
            $sender->setFood(20);
            $sender->setSaturation(20);
            $sender->sendMessage(TF::GREEN . "You have been fed!");
        }else{
            $target = $server->getPlayer($args[0]);
            if($target === null){
                $sender->sendMessage(TF::RED . "Player not found!");
                return;
            }
            $target->setFood(20);
            $target->setSaturation(20);
            $target->sendMessage(TF::GREEN . "You have been fed by " . TF::YELLOW . $sender->getName() . TF::GREEN . "!");
            $sender->sendMessage(TF::GREEN . "You have fed " . TF::YELLOW . $target->getName() . TF::GREEN . "!");
        }
    }

}