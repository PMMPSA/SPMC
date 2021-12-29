<?php

declare(strict_types=1);

namespace HyperFlareMC\PMRequisites\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\object\ExperienceOrb;
use pocketmine\entity\object\ItemEntity;
use pocketmine\Server;
use pocketmine\utils\TextFormat as TF;

class ClearLag extends Command{

    public function __construct(){
        parent::__construct(
            "clearlag",
            "Clear all ground entities!",
            TF::RED . "Usage: " . TF::GRAY . "/clearlag"
        );
        $this->setPermission("pmrequisites.clearlag");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        $server = Server::getInstance();
        if(!$sender->hasPermission("pmrequisites.clearlag")){
            $sender->sendMessage(TF::RED . "You do not have permission to use this command!");
            return;
        }
        if(count($args) !== 0){
            $sender->sendMessage($this->usageMessage);
            return;
        }
        $entitiesCleared = 0;
        foreach(Server::getInstance()->getLevels() as $level){
            foreach($level->getEntities() as $entity){
                if($entity instanceof ItemEntity or $entity instanceof ExperienceOrb){
                    $entity->flagForDespawn();
                    ++$entitiesCleared;
                }
            }
            $sender->sendMessage(TF::GREEN . "Successfully cleared " . TF::YELLOW . $entitiesCleared . TF::GREEN . " entities!");
        }
    }

}