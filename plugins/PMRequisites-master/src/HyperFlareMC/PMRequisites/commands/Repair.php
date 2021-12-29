<?php

declare(strict_types=1);

namespace HyperFlareMC\PMRequisites\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\Armor;
use pocketmine\item\Bow;
use pocketmine\item\Sword;
use pocketmine\item\Tool;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\utils\TextFormat as TF;

class Repair extends Command{

    public function __construct(){
        parent::__construct(
            "repair",
            "Repair the item in your hand!",
            TF::RED . "Usage: " . TextFormat::GRAY . "/repair"
        );
        $this->setPermission("pmrequisites.repair");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(!$sender->hasPermission("pmrequisites.repair")){
            $sender->sendMessage(TF::RED . "You do not have permission to use this command!");
            return;
        }
        if(!$sender instanceof Player){
            $sender->sendMessage(TF::RED . "You must be in-game to use this command!");
            return;
        }
        if(count($args) > 0){
            $sender->sendMessage($this->usageMessage);
            return;
        }
        $item = $sender->getInventory()->getItemInHand();
        if($item instanceof Armor or $item instanceof Sword or $item instanceof Bow or $item instanceof Tool){
            $item->setDamage(0);
            $sender->getInventory()->setItemInHand($item);
            $sender->sendMessage(TextFormat::GREEN . "Successfully repaired your " . TextFormat::YELLOW . $item->getName());
        }else{
            $sender->sendMessage(TextFormat::RED . "You cannot repair " . TextFormat::YELLOW . $item->getName() . TextFormat::RED . " with this command!");
            return;
        }
    }

}