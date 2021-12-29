<?php

namespace phuongaz\LOCMCore\Task;

use pocketmine\Server;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat as C;
use phuongaz\LOCMCore\Loader;
use phuongaz\LOCMCore\CParticle;
use pocketmine\math\Vector3;

class RankTask extends Task{

    public function onRun(int $currentTick) : void{
    	Loader::getInstance()->getRankManager()->Handling();
    }
}
