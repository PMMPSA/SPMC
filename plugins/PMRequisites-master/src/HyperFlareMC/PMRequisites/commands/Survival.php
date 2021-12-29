<?php

declare(strict_types=1);

namespace HyperFlareMC\PMRequisites\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat as TF;

class Survival extends Command{

    public function __construct(){
        parent::__construct(
            "survival",
            "Switch to Survival mode!",
            TF::RED . "Usage: " . TF::GRAY . "/survival or /survival <player>",
            ["gms"]
        );
        $this->setPermission("pmrequisites.survival");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        $server = Server::getInstance();
        if(!$sender->hasPermission("pmrequisites.survival")){
            $sender->sendMessage(TF::RED . "You do not have permission to use this command!");
            return;
        }
        if(!$sender instanceof Player){
            $sender->sendMessage(TF::RED . "You must be in-game to use this command!");
            return;
        }
        if($sender->getGamemode() === Player::SURVIVAL){
            $sender->sendMessage(TF::RED . "You are already in Survival!");
            return;
        }
        if(!isset($args[0])){
            $sender->setGamemode(Player::SURVIVAL);
            $sender->sendMessage(TF::GREEN . "Successfully switched to " . TF::YELLOW . "Survival " . TF::GREEN . "Mode!");
        }else{
            $target = $server->getPlayer($args[0]);
            if($target === null){
                $sender->sendMessage(TF::RED . "Player not found!");
                return;
            }
            if($target->getGamemode() === Player::SURVIVAL){
                $sender->sendMessage(TF::YELLOW . $target->getName() . TF::RED . " is already in Survival!");
                return;
            }
            $target->setGamemode(Player::SURVIVAL);
            $target->sendMessage(TF::GREEN . "You have been set to " . TF::YELLOW . "Survival " . TF::GREEN . "mode by " . TF::YELLOW . $sender->getName() . TF::GREEN . "!");
            $sender->sendMessage(TF::GREEN . "You have set " . TF::YELLOW . $target->getName() . TF::GREEN . " to " . TF::YELLOW . "Survival " . TF::GREEN . "mode!");
        }
    }
}