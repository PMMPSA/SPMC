<?php

declare(strict_types=1);

namespace HyperFlareMC\PMRequisites\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class Rename extends Command{

    public function __construct(){
        parent::__construct(
            "rename",
            "Rename the item in your hand!",
            TF::RED . "Usage: " . TF::GRAY . "/rename"
        );
        $this->setPermission("pmrequisites.rename");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(!$sender->hasPermission("pmrequisites.rename")){
            $sender->sendMessage(TF::RED . "You do not have permission to use this command!");
            return;
        }
        if(!$sender instanceof Player){
            $sender->sendMessage(TF::RED . "You must be in-game to use this command!");
            return;
        }
        if(count($args) === 0){
            $sender->sendMessage($this->usageMessage);
            return;
        }
        if(isset($args[0])){
            $name = implode(' ', $args);
            $item = $sender->getInventory()->getItemInHand();
            if($item->getId() === Item::AIR){
                $sender->sendMessage(TF::RED . "You can not rename Air!");
                return;
            }
            $sender->getInventory()->setItemInHand($item->setCustomName($name));
            $sender->sendMessage(TF::GREEN . "Successfully renamed your item to " . TF::YELLOW . $name . TF::GREEN . "!");
        }
    }
}