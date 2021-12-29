<?php

namespace phuongaz\LOCMCore\Task;

use pocketmine\command\ConsoleCommandSender;
use pocketmine\entity\Creature;
use pocketmine\entity\object\ItemEntity;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\TextFormat as C;
use phuongaz\LOCMCore\Loader;

class ClearLagTask extends Task{

    protected $time = 600; // 10 mintues.
    protected $music = 150;
    protected $is_music = null;

    public function onRun(int $currentTick){
        if(in_array($this->time, [30, 10, 5, 600])){
            $time = $this->time . " §7Giây§r";
            if($this->time >= 60){
                $time = floor(($this->time / 60) % 60) . " §fphút";
            }
            Loader::broadcast("§l§fCác vật phẩm dưới đất sẽ biến mất sau §e" . $time . "§r§7...§r");
        }
        if($this->music <= 0){
            Loader::getInstance()->getMusicManager()->play();
        }else{
            $this->music--;
        }
        if($this->is_music == null){
            Loader::getInstance()->getMusicManager()->play();
            $this->music = true;
        }
        if($this->time <= 0){
            $this->clearItems();
            $this->time = 600;
        }else{
            $this->time--;
        }
    }

    public function clearItems(): void{
        $count = 0;
        foreach(Server::getInstance()->getLevels() as $lvl){
            foreach($lvl->getEntities() as $en){
                if($en instanceof ItemEntity){
                    $count += 1;
                    $en->flagForDespawn();
                }
            }
        }
        Loader::broadcast("§l§f §7Đã dọn dẹp §e" . $count . "§f vật phẩm trên mặt đất.");
    }
}