<?php

declare(strict_types=1);

namespace HyperFlareMC\PMRequisites\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat as TF;

class ClearInventory extends Command{

    public function __construct(){
        parent::__construct(
            "clearinventory",
            "Clear your or another player's inventory!",
            TF::RED . "Usage: " . TF::GRAY . "/clearinventory or /clearinventory <player>",
            ["clearinv"]
        );
        $this->setPermission("pmrequisites.clearinventory");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        $server = Server::getInstance();
        if(!$sender->hasPermission("pmrequisites.clearinventory")){
            $sender->sendMessage(TF::RED . "You do not have permission to use this command!");
            return;
        }
        if(!$sender instanceof Player && count($args) === 0){
            $sender->sendMessage(TF::RED . "You must be in-game to clear your own inventory!");
            return;
        }
        if(!isset($args[0])){
            $sender->getInventory()->clearAll(true);
            $sender->getArmorInventory()->clearAll(true);
            $sender->sendMessage(TF::GREEN . "Successfully cleared your inventory!");
            return;
        }
        $target = $server->getPlayer($args[0]);
        if($target === null){
            $sender->sendMessage(TF::RED . "Player not found!");
            return;
        }
        $target->getInventory()->clearAll(true);
        $target->getArmorInventory()->clearAll(true);
        $sender->sendMessage(TF::GREEN . "Successfully cleared " . TF::YELLOW . $target->getName() . TF::GREEN . "'s inventory!");
        $target->sendMessage(TF::GREEN . "Your inventory has been cleared!");
    }
}