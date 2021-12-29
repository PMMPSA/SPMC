<?php

declare(strict_types=1);

namespace HyperFlareMC\PMRequisites\commands;

use HyperFlareMC\PMRequisites\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class Vanish extends Command{

    /**
     * @var Main
     */
    private $plugin;

    public function __construct(Main $plugin){
        parent::__construct(
            "vanish",
            "Switch to vanish mode!",
            TF::RED . "Usage: " . TF::GRAY . "/vanish",
            ["v"]
        );
        $this->setPermission("pmrequisites.vanish");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(!$sender->hasPermission("pmrequisites.vanish")){
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
        if($this->plugin->isSuperVanished($sender->getName())){
            $sender->sendMessage(TF::RED . "You must come out of supervanish before enabling vanish");
        }else{
            $this->toggleVanish($sender);
        }
    }

    public function toggleVanish(Player $player): void{
        if($this->plugin->isVanished($player->getName())){
            $this->plugin->setVanished($player, false, "vanish");
            $player->sendMessage(TF::GREEN . "You're no longer vanished!");
            foreach($this->plugin->getServer()->getOnlinePlayers() as $onlinePlayer){
                $onlinePlayer->showPlayer($player);
            }
        }else{
            $this->plugin->setVanished($player, true, "vanish");
            $player->sendMessage(TF::GREEN . "You are now vanished!");
            foreach($this->plugin->getServer()->getOnlinePlayers() as $onlinePlayer){
                if(!$this->plugin->isVanished($onlinePlayer->getName()) && $onlinePlayer !== $player){
                    $onlinePlayer->hidePlayer($player);
                }
            }
        }
    }

}