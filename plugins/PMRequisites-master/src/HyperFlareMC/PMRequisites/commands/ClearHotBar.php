<?php

declare(strict_types=1);

namespace HyperFlareMC\PMRequisites\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\utils\TextFormat as TF;

class ClearHotBar extends Command{

    public function __construct(){
        parent::__construct(
            "clearhotbar",
            "Clear your or another player's hotbar!",
            TF::RED . "Usage: " . TF::GRAY . "/clearhotbar or /clearhotbar <player>"
        );
        $this->setPermission("pmrequisites.clearhotbar");
    }

    private function clearHotbar(Player $target): void{
        for($i = 0; $i <= 8; $i++){
            $item = $target->getInventory()->getHotbarSlotItem($i);
            $target->getInventory()->removeItem($item);
        }
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        $server = Server::getInstance();
        if(!$sender->hasPermission("pmrequisites.clearhotbar")){
            $sender->sendMessage(TF::RED . "You do not have permission to use this command!");
            return;
        }
        if(!$sender instanceof Player && count($args) === 0){
            $sender->sendMessage(TF::RED . "You must be in-game to clear your own hotbar!");
            return;
        }
        if(!isset($args[0])){
            $this->clearHotbar($sender);
            $sender->sendMessage(TF::GREEN . "Successfully cleared your hotbar!");
            return;
        }
        $target = $server->getPlayer($args[0]);
        if($target === null){
            $sender->sendMessage(TF::RED . $args[0] . " is not online!");
            return;
        }
        $this->clearHotbar($target);
        $sender->sendMessage(TF::GREEN . "Successfully cleared " . TF::YELLOW . $target->getName() . TF::GREEN . "'s hotbar!");
        $target->sendMessage(TF::GREEN . "Your hotbar has been cleared!");
    }
}