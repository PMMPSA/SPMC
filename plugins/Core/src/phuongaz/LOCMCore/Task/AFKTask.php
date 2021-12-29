<?php

namespace phuongaz\LOCMCore\Task;

use pocketmine\scheduler\Task;
use phuongaz\LOCMCore\Loader;
class AFKTask extends Task {

    private $plugin;

    public function __construct(Loader $plugin)
    {
        $this->plugin = $plugin;
    }

    public function onRun(int $currentTick)
    {
        if(count(Loader::$times) < 1) return;
        foreach (Loader::$times as $player => $time){
            if(time() - $time >= 20000){
                $player = $this->plugin->getServer()->getPlayer($player);
                if($player){
                    $player->kick("BẠN TREO MÁY QUÁ LÂU", false);
                }
            }
        }
    }
}