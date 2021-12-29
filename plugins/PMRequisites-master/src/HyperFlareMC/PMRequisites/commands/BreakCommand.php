<?php

declare(strict_types=1);

namespace HyperFlareMC\PMRequisites\commands;

use pocketmine\block\Air;
use pocketmine\block\Block;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class BreakCommand extends Command{

    public function __construct(){
        parent::__construct(
            "break",
            "Break the block you are looking at!",
            TF::RED . "Usage: " . TF::GRAY . "/break"
        );
        $this->setPermission("pmrequisites.break");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(!$sender->hasPermission("pmrequisites.break")){
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
        if(($block = $sender->getTargetBlock(100, [Block::AIR])) === null){
            $sender->sendMessage(TF::RED . "That block is out of reach! Get closer!");
            return;
        }
        if($block->getName() === "Air"){
            $sender->sendMessage(TF::RED . "You can not break " . TF::YELLOW . "Air" . TF::RED . "!");
            return;
        }
        $sender->getLevel()->setBlock($block, new Air(), true, true);
        $sender->sendMessage(TF::GREEN . "Successfully broke " . TF::YELLOW . $block->getName() . TF::GREEN . "!");
    }

}