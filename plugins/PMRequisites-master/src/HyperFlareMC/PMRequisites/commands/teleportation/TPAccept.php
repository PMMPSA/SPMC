<?php

declare(strict_types=1);

namespace HyperFlareMC\PMRequisites\commands\teleportation;

use HyperFlareMC\PMRequisites\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class TPAccept extends Command{

    /**
     * @var Main
     */
    private $plugin;

    public function __construct(Main $plugin){
        parent::__construct(
            "tpaccept",
            "Accept a teleport request!",
            TF::RED . "Usage: " . TF::GRAY . "/tpaccept",
            ["tpyes"]
        );
        $this->setPermission("pmrequisites.teleportation.tpaccept");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(!$sender->hasPermission("pmrequisites.teleportation.tpaccept")){
            $sender->sendMessage(TF::RED . "You do not have permission to use this command!");
            return false;
        }
        if(!$sender instanceof Player){
            $sender->sendMessage(TF::RED . "You must be in-game to use this command!");
            return false;
        }
        if(!($request = $this->plugin->hasIncomingRequest($sender))){
            $sender->sendMessage(TF::RED . "You don't have any teleport requests!");
            return false;
        }
        switch(count($args)){
            case 0:
                if(!(($player = $this->plugin->getLastIncomingRequest($sender)) instanceof Player)){
                    $sender->sendMessage(TF::YELLOW . $player . TF::RED . " is no longer online!");
                    return false;
                }
                break;
            default:
                $sender->sendMessage($this->usageMessage);
                return false;
                break;
        }
        $request = $this->plugin->getRequestType($player);
        if($request === "tphere"){
            $player->sendMessage(TF::YELLOW . $sender->getName() . TF::GREEN . " accepted your teleport request!");
            $sender->sendMessage(TF::GREEN . "You have been teleported to " . TF::YELLOW . $player->getName() . TF::GREEN . "!");
            $sender->teleport($player);
        }else{
            $player->sendMessage(TF::YELLOW . $sender->getName() . TF::GREEN . " accepted your teleport request!");
            $sender->sendMessage(TF::YELLOW . $player->getName() . TF::GREEN . " has been teleported to you!");
            $player->teleport($sender);
        }
        $this->plugin->removeTPRequest($player);
        return true;
    }

}