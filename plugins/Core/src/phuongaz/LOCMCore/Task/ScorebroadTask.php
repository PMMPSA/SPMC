<?php

namespace phuongaz\LOCMCore\Task;

use pocketmine\scheduler\Task;
use pocketmine\Server;
use phuongaz\LOCMCore\CParticle;
use pocketmine\math\Vector3;

use phuongaz\LOCMCore\Loader;
class ScorebroadTask extends Task
{
    public function onRun(Int $currentTick)
    {
        foreach(Server::getInstance()->getOnlinePlayers() as $p) {
            if(Loader::getInstance()->isDev($p)) {
                Loader::getInstance()->getScoreManager()->addScore($p, "Developer");
            }else{
            	Loader::getInstance()->getScoreManager()->addScore($p, "Member");
            }
        }
                $level = Loader::getInstance()->getServer()->getLevelByName("newvid");
        $cpos = new Vector3(51.5, 25, -19.4);
        for($i = 5; $i > 0; $i -= 0.1){
            $radio = $i / 3;
            $x = $radio * cos(3 * $i);
            $y = 5 - $i;
            $z = $radio * sin(3 * $i);
            $level->addParticle(new CParticle(CParticle::BLUE_FLAME, $cpos->add($x, $y, $z)));
        }
        for($i = 5; $i > 0; $i -= 0.1){
            $radio = $i / 3;
            $x = -$radio * cos(3 * $i);
            $y = 5 - $i;
            $z = -$radio * sin(3 * $i);
            $level->addParticle(new CParticle(CParticle::BLUE_FLAME, $cpos->add($x, $y, $z)));
        }
    }
}