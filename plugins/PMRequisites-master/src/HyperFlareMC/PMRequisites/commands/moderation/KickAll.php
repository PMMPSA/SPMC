<?php

declare(strict_types=1);

namespace HyperFlareMC\PMRequisites\commands\moderation;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;

class KickAll extends Command{

    public function __construct(){
        parent::__construct(
            "kickall",
            "Kick all players!",
            TF::RED . "Usage: " . TF::GRAY . "/kickall <reason>"
        );
        $this->setPermission("pmrequisites.moderation.kickall");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(!$sender->hasPermission("pmrequisites.moderation.kickall")){
            $sender->sendMessage(TF::RED . "You do not have permission to use this command!");
            return;
        }
        if($args !== []){
            $reason = implode(" ", $args);
        }else{
            $reason = "Unspecified";
        }
        foreach($sender->getServer()->getOnlinePlayers() as $player){
            if($player !== $sender){
                $player->kick("Everyone has been kicked!" . TF::EOL . "Reason: " . $reason, false);
            }
        }
        $sender->sendMessage(TF::GREEN . "Kicked all the players for " . TF::YELLOW . $reason . TF::GREEN . "!");
    }

}