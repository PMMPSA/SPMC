<?php

declare(strict_types=1);

namespace HyperFlareMC\PMRequisites\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class Ping extends Command{

    public function __construct(){
        parent::__construct(
            "ping",
            "Check your or another player's ping!",
            TF::RED . "Usage: " . TF::GRAY . "/ping or /ping <player>"
        );
        $this->setPermission("pmrequisites.ping");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(!$sender->hasPermission("pmrequisites.ping")){
            $sender->sendMessage(TF::RED . "You do not have permission to use this command!");
            return;
        }
        if(!$sender instanceof Player && count($args) === 0){
            $sender->sendMessage(TF::RED . "You must be in-game to check your own ping!");
            return;
        }
        if(!isset($args[0])){
            $senderPing = $sender->getPing();
            if($senderPing >= 250){
                $sender->sendMessage(TF::GREEN . "Your ping is " . TF::RED . $senderPing . "ms");
                return;
            }elseif($senderPing >= 150){
                $sender->sendMessage(TF::GREEN . "Your ping is " . TF::YELLOW . $senderPing . "ms");
                return;
            }elseif($senderPing >= 0){
                $sender->sendMessage(TF::GREEN . "Your ping is " . $senderPing . "ms");
                return;
            }
        }else{
            $target = $sender->getServer()->getPlayer($args[0]);
            if($target === null){
                $sender->sendMessage(TF::RED . "Player not found!");
                return;
            }
            $targetPing = $target->getPing();
            if($targetPing >= 250){
                $sender->sendMessage(TF::YELLOW . $target->getName() . TF::GREEN . "'s ping is " . TF::RED . $targetPing . "ms");
                return;
            }elseif($targetPing >= 150){
                $sender->sendMessage(TF::YELLOW . $target->getName() . TF::GREEN . "'s ping is " . TF::YELLOW . $targetPing . "ms");
                return;
            }elseif($targetPing >= 0){
                $sender->sendMessage(TF::YELLOW . $target->getName() . TF::GREEN . "'s ping is " . $targetPing  . "ms");
                return;
            }
        }
    }

}