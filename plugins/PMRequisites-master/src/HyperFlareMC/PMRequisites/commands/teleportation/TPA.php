<?php

declare(strict_types=1);

namespace HyperFlareMC\PMRequisites\commands\teleportation;

use HyperFlareMC\PMRequisites\Main;
use pocketmine\command\Command;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;
use pocketmine\command\CommandSender;

class TPA extends Command{

    /**
     * @var Main
     */
    private $plugin;

    public function __construct(Main $plugin){
        parent::__construct(
            "tpa",
            "Request to teleport to another player!",
            TF::RED . "Usage: " . TF::GRAY . "/tpa <player>",
            ["tpask"]
        );
        $this->plugin = $plugin;
        $this->setPermission("pmrequisites.teleporation.tpa");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(!$sender->hasPermission("pmrequisites.teleporation.tpa")){
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
            if($this->plugin->isSuperVanished($args[0])){
                $sender->sendMessage(TF::YELLOW . $args[0] . TF::RED . " is not online!");
                return false;
            }
            $this->plugin->addTPRequest($sender, $player, "tpa");
            $player->sendMessage(TF::YELLOW . $sender->getName() . TF::GREEN . " wants to teleport to you, use: " . TF::EOL . TF::YELLOW . "/tpaccept" . TF::GREEN . " to accept" . TF::EOL . "or" . TF::EOL . TF::YELLOW . "/tpdeny" . TF::GREEN . " to deny");
            $sender->sendMessage(TF::GREEN . "Teleport request successfully sent to " . TF::YELLOW . $player->getName() . TF::GREEN . "!");
            return true;
        }else{
            $sender->sendMessage($this->usageMessage);
        }
        return true;
    }

}