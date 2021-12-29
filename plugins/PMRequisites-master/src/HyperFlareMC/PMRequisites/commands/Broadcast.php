<?php

declare(strict_types=1);

namespace HyperFlareMC\PMRequisites\commands;

use HyperFlareMC\PMRequisites\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use pocketmine\utils\TextFormat as TF;

class Broadcast extends Command{

    /**
     * @var Main
     */
    private $plugin;

    public function __construct(Main $plugin){
        parent::__construct(
            "broadcast",
            "Broadcast a message to the server!",
            TF::RED . "Usage: " . TF::GRAY . "/broadcast <message>",
            ["bc", "bcast"]
        );
        $this->setPermission("pmrequisites.broadcast");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        $server = Server::getInstance();
        if(!$sender->hasPermission("pmrequisites.broadcast")){
            $sender->sendMessage(TF::RED . "You do not have permission to use this command!");
            return;
        }
        if(count($args) === 0){
            $sender->sendMessage($this->usageMessage);
            return;
        }
        if(isset($args[0])){
            $prefix = $this->plugin->broadcastPrefix;
            $msg = implode(" ", $args);
            $server->broadcastMessage($prefix . $msg);
        }
    }
}