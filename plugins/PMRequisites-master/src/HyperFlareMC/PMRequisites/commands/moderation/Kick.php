<?php

declare(strict_types=1);

namespace HyperFlareMC\PMRequisites\commands\moderation;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat as TF;

class Kick extends Command{

    public function __construct(){
        parent::__construct(
            "kick",
            "Kick another player from the server!",
            TF::RED . "Usage: " . TF::GRAY . "/kick <player> <reason>"
        );
        $this->setPermission("pmrequisites.moderation.kick");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        $server = Server::getInstance();
        if(!$sender->hasPermission("pmrequisites.moderation.kick")){
            $sender->sendMessage(TF::RED . "You do not have permission to use this command!");
            return;
        }
        if(isset($args[0])){
            $name = array_shift($args);
            if($args !== []){
                $reason = implode(" ", $args);
            }else{
                $reason = "Unspecified";
            }
            $target = $server->getPlayer($name);
            if(!($target instanceof Player)){
                $sender->sendMessage(TF::YELLOW . $name . TF::RED . " is not online!");
                return;
            }
            $target->kick("You've been kicked!" . TF::EOL . "Reason: " . $reason, false);
            $sender->sendMessage(TF::GREEN . "Successfully kicked " . TF::YELLOW . $name . TF::GREEN . " for " . TF::YELLOW . $reason . TF::GREEN . "!");
        }else{
            $sender->sendMessage($this->usageMessage);
        }
    }

}