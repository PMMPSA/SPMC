<?php

declare(strict_types=1);

namespace HyperFlareMC\PMRequisites\commands;

use HyperFlareMC\PMRequisites\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\PlayerListPacket;
use pocketmine\network\mcpe\protocol\types\PlayerListEntry;
use pocketmine\network\mcpe\protocol\types\SkinAdapterSingleton;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;

class SuperVanish extends Command{

    /**
     * @var Main
     */
    private $plugin;

    public function __construct(Main $plugin){
        parent::__construct(
            "supervanish",
            "Go into vanish mode, but more super!",
            TF::RED . "Usage: " . TF::GRAY . "/supervanish",
            ["sv"]
        );
        $this->setPermission("pmrequisites.supervanish");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(!$sender->hasPermission("pmrequisites.supervanish")){
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
        if($this->plugin->isVanished($sender->getName())){
            $sender->sendPopup(TF::RED . "You need to come out of Vanish before enabling SV!");
        }else{
            $this->toggleSuperVanish($sender);
        }
    }

    public function toggleSuperVanish(Player $player): void{
        if($this->plugin->isSuperVanished($player->getName())){
            $this->plugin->setVanished($player, false, "supervanish");

            $joinMessage = $this->plugin->superVanishJoinMessage;
            $replacements = [
                "{player}" => $player->getName()
            ];
            foreach($replacements as $tag => $def){
                $sendJoinMessage = str_replace($tag, $def, $joinMessage);
            }
            $this->plugin->getServer()->broadcastMessage($sendJoinMessage);

            $player->sendMessage(TF::GREEN . "You are no longer supervanished!");
            $pk = new PlayerListPacket();
            $pk->type = PlayerListPacket::TYPE_ADD;
            $pk->entries[] = PlayerListEntry::createAdditionEntry($player->getUniqueId(), $player->getId(), $player->getDisplayName(), SkinAdapterSingleton::get()->toSkinData($player->getSkin()), $player->getXuid());
            foreach($this->plugin->getServer()->getOnlinePlayers() as $onlinePlayer){
                $onlinePlayer->showPlayer($player);
                $onlinePlayer->sendDataPacket($pk);
            }
        }else{
            $this->plugin->setVanished($player, true, "supervanish");

            $leaveMessage = $this->plugin->superVanishLeaveMessage;
            $replacements = [
                "{player}" => $player->getName()
            ];
            foreach($replacements as $tag => $def){
                $sendLeaveMessage = str_replace($tag, $def, $leaveMessage);
            }
            $this->plugin->getServer()->broadcastMessage($sendLeaveMessage);

            $player->sendMessage(TF::GREEN . "You are now supervanished!");
            $entry = new PlayerListEntry();
            $entry->uuid = $player->getUniqueId();
            $pk = new PlayerListPacket();
            $pk->entries[] = $entry;
            $pk->type = PlayerListPacket::TYPE_REMOVE;
            foreach($this->plugin->getServer()->getOnlinePlayers() as $onlinePlayer){
                if($onlinePlayer !== $player){
                    $onlinePlayer->hidePlayer($player);
                    $onlinePlayer->sendDataPacket($pk);
                }
            }
        }
    }

}