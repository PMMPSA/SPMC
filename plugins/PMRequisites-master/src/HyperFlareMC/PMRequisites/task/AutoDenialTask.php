<?php

declare(strict_types=1);

namespace HyperFlareMC\PMRequisites\task;

use HyperFlareMC\PMRequisites\Main;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat as TF;

class AutoDenialTask extends Task{

    /**
     * @var Main
     */
    private $plugin;

    /**
     * @var Player
     */
    private $requester;

    /**
     * @var Player
     */
    private $target;

    public function __construct(Main $plugin, Player $requester, Player $target){
        $this->plugin = $plugin;
        $this->requester = $requester;
        $this->target = $target;
    }

    public function onRun(int $currentTick){
        if(!is_null($this->requester) && !is_null($this->target)){
            $this->requester->sendMessage(TF::RED . "Teleport request to " . TF::YELLOW . $this->target->getName() . TF::RED . " has been automatically declined!");
            $this->plugin->removeTPRequest($this->requester);
        }
    }

}