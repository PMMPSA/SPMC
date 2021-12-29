<?php

declare(strict_types=1);

namespace HyperFlareMC\PMRequisites\commands\teleportation;

use HyperFlareMC\PMRequisites\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class TPAHere extends Command{

    /**
     * @var Main
     */
    private $plugin;

    public function __construct(Main $plugin){
        parent::__construct(
            "tpahere",
            "Request to teleport another player to you!",
            TF::RED . "Usage: " . TF::GRAY . "/tpahere <player>"
        );
        $this->setPermission("pmrequisites.teleportation.tpahere");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(!$sender->hasPermission("pmrequisites.teleportation.tpahere")){
            $sender->sendMessage(TF::RED . "You do not have permission to use this command!");
            return false;
        }
        if(!$sender instanceof Player){
            $sender->sendMessage(TF::RED . "You must be in-game to use this command!");
            return false;
        }
        if(isset($args[0])){
            if($this->plugin->hasOutgoingRequest($sender)){
                $sender->sendMessage(TF::RED . "You already have an outgoing request!");
                return false;
            }
            if(!($player = $this->plugin->getServer()->getPlayer($args[0]))){
                $sender->sendMessage(TF::YELLOW . $args[0] . TF::RED . " is not online!");
                return false;
            }
            if($player->getName() === $sender->getName()){
                $sender->sendMessage(TF::RED . "You can't teleport to yourself!");
                return false;
            }
            $this->plugin->addTPRequest($sender, $player, "tphere");
            $player->sendMessage(TF::YELLOW . $sender->getName() . TF::GREEN . " wants you to teleport to them, use: " . TF::EOL . TF::YELLOW . "/tpaccept " . TF::GREEN . " to accept" . TF::EOL . "or" . TF::EOL . TF::YELLOW . "/tpdeny" . TF::GREEN . " to deny");
            $sender->sendMessage(TF::GREEN . "Teleport request successfully sent to " . TF::YELLOW . $player->getName() . TF::GREEN . "!");
            return true;
        }else{
            $sender->sendMessage($this->usageMessage);
        }
        return true;
    }

}