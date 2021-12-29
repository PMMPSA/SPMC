<?php

declare(strict_types=1);

namespace HyperFlareMC\PMRequisites\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class Nick extends Command{

    private $data = [];

    public function __construct(){
        parent::__construct(
            "nick",
            "Nick yourself or another player!",
            TF::RED . "Usage: " . TF::GRAY . "/nick <nickname> or /nick off",
            ["nickname"]
        );
        $this->setPermission("pmrequisites.nick");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(!$sender->hasPermission("pmrequisites.nick")){
            $sender->sendMessage(TF::RED . "You do not have permission to use this command!");
            return;
        }
        if(count($args) === 0){
            $sender->sendMessage($this->usageMessage);
            return;
        }
        if($args[0] == "list"){
            foreach($this->data as $ogname => $currentname){
                $sender->sendMessage($ogname ." -> ".$currentname);
            }
            return;
        }                
        if(!$sender instanceof Player){
            $sender->sendMessage(TF::RED . "You must be in-game to use this command!");
            return;
        }


        if($args[0] === "off"){
            if(isset($this->data[$sender->getName()])){
                unset($this->data[$sender->getName()]);
            }
            $sender->setDisplayName($sender->getName());
            $sender->sendMessage(TF::GREEN . "Your nickname has been disabled!");
        }else{
            $this->data[$sender->getName()] = implode(" ", $args);
            $sender->setDisplayName(($nick = implode(" ", $args)));
            $sender->sendMessage(TF::GREEN . "Changed your nick to " . TF::YELLOW . $nick);
        }
    }
}