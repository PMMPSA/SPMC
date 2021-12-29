<?php

declare(strict_types=1);

namespace HyperFlareMC\PMRequisites\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat as TF;

class Sudo extends Command{

    public function __construct(){
        parent::__construct(
            "sudo",
            "Execute a command or chat as another player!",
            TF::RED . "Usage: " . TF::GRAY . "/sudo <player> </command|chat>"
        );
        $this->setPermission("pmrequisites.sudo");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        $server = Server::getInstance();
        if(!$sender->hasPermission("pmrequisites.sudo")){
            $sender->sendMessage(TF::RED . "You do not have permission to use this command!");
            return false;
        }
        if((!count($args)) >= 2){
            $sender->sendMessage($this->usageMessage);
            return false;
        }

        $name = array_shift($args);
        $target = $server->getPlayer($name);

        if($target === $sender){
            $sender->sendMessage(TF::RED . "You can not sudo yourself!");
            return false;
        }
        if(!$target instanceof Player){
            $sender->sendMessage(TF::RED . "Player not found!");
            return false;
        }

        $msg = implode(" ", $args);

        if(strpos($msg, "/") === false){
            $target->chat($msg);
            $sender->sendMessage(TF::GREEN . "Chat sent as " . TF::YELLOW . $target->getName());
        }else{
            $server->getCommandMap()->dispatch($target, ltrim($msg, "/"));
            $sender->sendMessage(TF::GREEN . "Command executed as " . TF::YELLOW . $target->getName());
        }
        return true;
    }

}