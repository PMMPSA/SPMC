<?php

declare(strict_types=1);

namespace HyperFlareMC\PMRequisites\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat as TF;

class ClearHand extends Command{

    public function __construct(){
        parent::__construct(
            "clearhand",
            "Clear the item in your or another player's hand!",
            TF::RED . "Usage: " . TF::GRAY . "/clearhand or /clearhand <player>"
        );
        $this->setPermission("pmrequisites.clearhand");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        $server = Server::getInstance();
        if(!$sender->hasPermission("pmrequisites.clearhand")){
            $sender->sendMessage(TF::RED . "You do not have permission to use this command!");
            return;
        }
        if(!$sender instanceof Player && count($args) === 0){
            $sender->sendMessage(TF::RED . "You must be in-game to clear your own hand!");
            return;
        }
        if(!isset($args[0])){
            $sender->getInventory()->setItemInHand(ItemFactory::get(Item::AIR));
            $sender->sendMessage(TF::GREEN . "Successfully cleared your hand!");
        }else{
            $target = $server->getPlayer($args[0]);
            if($target === null){
                $sender->sendMessage(TF::RED . "Player not found!");
                return;
            }
            $target->getInventory()->setItemInHand(ItemFactory::get(Item::AIR));
            $sender->sendMessage(TF::GREEN . "Successfully cleared " . TF::YELLOW . $target->getName() . TF::GREEN . "'s hand!");
            $target->sendMessage(TF::GREEN . "Your hand has been cleared!");
        }
    }

}