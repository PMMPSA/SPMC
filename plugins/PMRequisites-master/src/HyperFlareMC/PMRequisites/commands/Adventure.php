<?php

declare(strict_types=1);

namespace HyperFlareMC\PMRequisites\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat as TF;

class Adventure extends Command{

    public function __construct(){
        parent::__construct(
            "adventure",
            "Switch to Adventure mode!",
            TF::RED . "Usage: " . TF::GRAY . "/adventure or /adventure <player>",
            ["gma"]
        );
        $this->setPermission("pmrequisites.adventure");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        $server = Server::getInstance();
        if(!$sender->hasPermission("pmrequisites.adventure")){
            $sender->sendMessage(TF::RED . "You do not have permission to use this command!");
            return;
        }
        if(!$sender instanceof Player){
            $sender->sendMessage(TF::RED . "You must be in-game to use this command!");
            return;
        }
        if($sender->getGamemode() === Player::ADVENTURE){
            $sender->sendMessage(TF::RED . "You are already in Adventure!");
            return;
        }
        if(!isset($args[0])){
            $sender->setGamemode(Player::ADVENTURE);
            $sender->sendMessage(TF::GREEN . "Successfully switched to " . TF::YELLOW . "Adventure " . TF::GREEN . "Mode!");
        }else{
            $target = $server->getPlayer($args[0]);
            if($target === null){
                $sender->sendMessage(TF::RED . "Player not found!");
                return;
            }
            if($target->getGamemode() === Player::ADVENTURE){
                $sender->sendMessage(TF::YELLOW . $target->getName() . TF::RED . " is already in Adventure!");
                return;
            }
            $target->setGamemode(Player::ADVENTURE);
            $target->sendMessage(TF::GREEN . "You have been set to " . TF::YELLOW . "Adventure " . TF::GREEN . "mode by " . TF::YELLOW . $sender->getName() . TF::GREEN . "!");
            $sender->sendMessage(TF::GREEN . "You have set " . TF::YELLOW . $target->getName() . TF::GREEN . " to " . TF::YELLOW . "Adventure " . TF::GREEN . "mode!");
        }
    }
}