<?php

namespace TungstenVn\MineLevel;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use RedCraftPE\RedSkyBlock\Skyblock;

use pocketmine\event\player\PlayerJoinEvent;
class MineLevel extends PluginBase implements Listener
{


    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveDefaultConfig();
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        if ($sender instanceof Player) {
           if(!isset($args[0])){
               $level = $this->getLevel($sender);
               $block = $this->getBlock($sender);
               $sender->sendMessage("§aMine§7Level >§eLevel của bạn:§c $level");
               $sender->sendMessage(" §e ->Tổng số block đã đào:§b $block");
               $marker = $this->getConfig()->getNested("marker");
               if(!isset($marker[$level +1])){
                   $sender->sendMessage(" §e ->Đã đạt level cao nhất!");
               }else{
                   $sender->sendMessage(" §e ->Số block cần để lên cấp:§6 ".$marker[$level +1]);
               }

           }else{
               a:
               $player = $this->getServer()->getPlayer($args[0]);
               if (null != $player) {
                   $level = $this->getLevel($player);
                   $block = $this->getBlock($player);
                   $name = $player->getName();
                   $sender->sendMessage("§aMine§7Level >§eLevel của §6$name:§c $level");
                   $sender->sendMessage(" §e ->Tổng block đã đào:§b $block");
               } else {
                   $sender->sendMessage("§aMine§7Level > §cKhông thấy player này");
               }
           }
        } else {
            if(isset($args[0])){
                goto a;
            }else{
                $sender->sendMessage("Missing name,console cant use this command alone");
            }
        };
        return true;
    }
    public function getLevel(Player $player){
        $name = $player->getName();
        $level = $this->getConfig()->getNested("database")[$name]["level"];
        if (isset($level)) {
            return $level;
        }else{
            return "DataMissing";
        }
    }
    public function getBlock(Player $player){
        $name = $player->getName();
        $block = $this->getConfig()->getNested("database")[$name]["block"];
        if (isset($block)) {
            return $block;
        }else{
            return "data Missing";
        }
    }
    
    public function onBreak(BlockBreakEvent $e)
    {
        if($e->isCancelled()){
          return;
        }
        
        if(!Skyblock::getInstance()->checkIsland($e->getPlayer(), $e->getBlock())){
          return;
        }

        $name = $e->getPlayer()->getName();
        if (!isset($this->getConfig()->getNested("database")[$name]["block"]) || !isset($this->getConfig()->getNested("database")[$name]["level"])) {
            print("There is fatal problem at MineLevel,missing data of $name");
            return;
        }
        $block = $this->getConfig()->getNested("database")[$name]["block"];
        $level = $this->getConfig()->getNested("database")[$name]["level"];
        $this->getConfig()->setNested("database.$name.block", $block + 1);
        $this->getConfig()->save();

        $marker = $this->getConfig()->getNested("marker");
        if(!isset($marker[$level +1])){
            return;
        }
        if ($marker != null) {
            if ($block + 1 >= $marker[$level + 1]) {
                $this->getConfig()->setNested("database.$name.level", $level + 1);
                $this->getConfig()->save();
                $level = $level + 1;
                $block = $block + 1;
                if(!isset($marker[$level +1])){
                    $this->getServer()->broadcastMessage("§eMineLevel >§r Chúc mừng §e$name §rđã §cđạt level cao nhất §rcủa §aMine§7Level§r,level:§b$level §r,Tổng block đã đào:§6 $block");
                }else{
                    $this->getServer()->broadcastMessage("§eMineLevel >§r Chúc mừng §e$name §rđã lên level §c$level §r,Tổng block đã đào:§6 $block");
                }
            }
        } else {
            print("There are nothing on 'marker' on MineLevel's config, pls check, dont leave it empty");
            return;
        }
    }

    public function onJoin(PlayerJoinEvent $e)
    {
        $name = $e->getPlayer()->getName();
        if (!isset($this->getConfig()->getNested("database")[$name])) {
            $this->getConfig()->setNested("database.$name.block", 0);
            $this->getConfig()->setNested("database.$name.level", 0);
            $this->getConfig()->save();
        }
    }
}