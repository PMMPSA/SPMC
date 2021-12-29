<?php

declare(strict_types=1);

namespace HyperFlareMC\PMRequisites\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat as TF;

class Creative extends Command{

    public function __construct(){
        parent::__construct(
            "creative",
            "Switch to Creative mode!",
            TF::RED . "Usage: " . TF::GRAY . "/creative or /creative <player>",
            ["gmc"]
        );
        $this->setPermission("pmrequisites.creative");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        $server = Server::getInstance();
        if(!$sender->hasPermission("pmrequisites.creative")){
            $sender->sendMessage(TF::RED . "You do not have permission to use this command!");
            return;
        }
        if(!$sender instanceof Player){
            $sender->sendMessage(TF::RED . "You must be in-game to use this command!");
            return;
        }
        if($sender->getGamemode() === Player::CREATIVE){
            $sender->sendMessage(TF::RED . "You are already in Creative!");
            return;
        }
        if(!isset($args[0])){
            $sender->setGamemode(Player::CREATIVE);
            $sender->sendMessage(TF::GREEN . "Successfully switched to " . TF::YELLOW . "Creative " . TF::GREEN . "Mode!");
        }else{
            $target = $server->getPlayer($args[0]);
            if($target === null){
                $sender->sendMessage(TF::RED . "Player not found!");
                return;
            }
            if($target->getGamemode() === Player::CREATIVE){
                $sender->sendMessage(TF::YELLOW . $target->getName() . TF::RED . " is already in Creative!");
                return;
            }
            $target->setGamemode(Player::CREATIVE);
            $target->sendMessage(TF::GREEN . "You have been set to " . TF::YELLOW . "Creative " . TF::GREEN . "mode by " . TF::YELLOW . $sender->getName() . TF::GREEN . "!");
            $sender->sendMessage(TF::GREEN . "You have set " . TF::YELLOW . $target->getName() . TF::GREEN . " to " . TF::YELLOW . "Creative " . TF::GREEN . "mode!");
        }
    }
}