<?php

declare(strict_types=1);

namespace HyperFlareMC\PMRequisites;

use HyperFlareMC\PMRequisites\commands\Adventure;
use HyperFlareMC\PMRequisites\commands\BreakCommand;
use HyperFlareMC\PMRequisites\commands\Broadcast;
use HyperFlareMC\PMRequisites\commands\ClearHand;
use HyperFlareMC\PMRequisites\commands\ClearHotBar;
use HyperFlareMC\PMRequisites\commands\ClearInventory;
use HyperFlareMC\PMRequisites\commands\ClearLag;
use HyperFlareMC\PMRequisites\commands\Compass;
use HyperFlareMC\PMRequisites\commands\Creative;
use HyperFlareMC\PMRequisites\commands\Feed;
use HyperFlareMC\PMRequisites\commands\Fly;
use HyperFlareMC\PMRequisites\commands\GetPos;
use HyperFlareMC\PMRequisites\commands\Heal;
use HyperFlareMC\PMRequisites\commands\moderation\Kick;
use HyperFlareMC\PMRequisites\commands\moderation\KickAll;
use HyperFlareMC\PMRequisites\commands\Nick;
use HyperFlareMC\PMRequisites\commands\Ping;
use HyperFlareMC\PMRequisites\commands\Rename;
use HyperFlareMC\PMRequisites\commands\Repair;
use HyperFlareMC\PMRequisites\commands\SetSpawn;
use HyperFlareMC\PMRequisites\commands\Spawn;
use HyperFlareMC\PMRequisites\commands\Sudo;
use HyperFlareMC\PMRequisites\commands\SuperVanish;
use HyperFlareMC\PMRequisites\commands\Survival;
use HyperFlareMC\PMRequisites\commands\Vanish;
use HyperFlareMC\PMRequisites\task\AutoDenialTask;
use HyperFlareMC\PMRequisites\commands\teleportation\TPA;
use HyperFlareMC\PMRequisites\commands\teleportation\TPAccept;
use HyperFlareMC\PMRequisites\commands\teleportation\TPAHere;
use HyperFlareMC\PMRequisites\commands\teleportation\TPDeny;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase{

    /**
     * @var array
     */
    private $TPRequests = [];

    /**
     * @var array
     */
    private $lastRequest = [];

    /**
     * @var array
     */
    private $supervanished = [];

    /**
     * @var array
     */
    private $vanished = [];

    /**
     * @var Config
     */
    private $config;

    /**
     * @var string
     */
    public $superVanishJoinMessage;

    /**
     * @var string
     */
    public $superVanishLeaveMessage;

    /**
     * @var string
     */
    public $broadcastPrefix;

    public function onEnable() : void{
        $this->vanished = [];
        $this->supervanished = [];
        $this->config = $this->getConfig()->getAll();
        $this->superVanishJoinMessage = $this->config["super-vanish-join"];
        $this->superVanishLeaveMessage = $this->config["super-vanish-leave"];
        $this->broadcastPrefix = $this->config["broadcast-prefix"];
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $commands = [
            "say",
            "me",
            "kick"
        ];
        $this->unregisterCommands($commands);
        $this->registerCommands();
    }

    /**
     * @param array $commands
     */
    private function unregisterCommands(array $commands) : void{
        $commandMap = $this->getServer()->getInstance()->getCommandMap();
        foreach($commandMap->getCommands() as $cmd){
            if(in_array($cmd->getName(), $commands)){
                $cmd->setLabel("disabled_" . $cmd->getName());
                $commandMap->unregister($cmd);
            }
        }
    }

    public function registerCommands() : void{
        $this->getServer()->getCommandMap()->registerAll($this->getName(), [
            new Adventure(),
            new BreakCommand(),
            new Broadcast($this),
            new ClearHand(),
            new ClearHotBar(),
            new ClearInventory(),
            new ClearLag(),
            new Compass(),
            new Creative(),
            new Feed(),
            new Fly(),
            new GetPos(),
            new Heal(),
            new Kick(),
            new KickAll(),
            new Nick(),
            new Ping(),
            new Rename(),
            new Repair(),
            new SetSpawn(),
            new Spawn(),
            new Sudo(),
            new SuperVanish($this),
            new Survival(),
            new TPA($this),
            new TPAccept($this),
            new TPAHere($this),
            new TPDeny($this),
            new Vanish($this),
            ]);
    }

    /**
     * @param string $player
     * @return bool
     */
    public function isSuperVanished(string $player): bool{
        if(in_array($player, $this->supervanished)){
            return true;
        }
        return false;
    }

    /**
     * @param Player $target
     * @param bool $mode
     * @param string $type
     */
    public function setVanished(Player $target, bool $mode, string $type): void{
        if($mode === true){
            if($type === "vanish"){
                array_push($this->vanished, $target->getName());
            }else{
                array_push($this->supervanished, $target->getName());
            }
        }else{
            if($type === "vanish"){
                $this->vanished = array_diff($this->vanished, [$target->getName()]);
            }else{
                $this->supervanished = array_diff($this->supervanished, [$target->getName()]);
            }
        }
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function isHidden(Player $player): bool{
        if($this->isVanished($player->getName()) || $this->isSuperVanished($player->getName())){
            return true;
        }
        return false;
    }

    /**
     * @param string $player
     * @return bool
     */
    public function isVanished(string $player): bool{
        if(in_array($player, $this->vanished)){
            return true;
        }
        return false;
    }

    /**
     * @param Player $requester
     * @param Player $target
     * @param string $type
     */
    public function addTPRequest(Player $requester, Player $target, string $type): void{
        $task = $this->getScheduler()->scheduleDelayedTask(new AutoDenialTask($this, $requester, $target), 3000);
        $this->TPRequests[$requester->getName()] = [$type, $target->getName(), $task->getTaskId()];
        $this->lastRequest[$target->getName()] = $requester->getName();
    }

    /**
     * @param Player $requester
     */
    public function removeTPRequest(Player $requester): void{
        $data = $this->TPRequests[$requester->getName()];
        unset($this->TPRequests[$requester->getName()]);
        $this->getScheduler()->cancelTask($data[2]);
    }

    /**
     * @param Player $requester
     * @return string
     */
    public function getRequestType(Player $requester): string{
        return $this->TPRequests[$requester->getName()][0];
    }

    /**
     * @param Player $target
     * @return mixed|Player|null
     */
    public function getLastIncomingRequest(Player $target){
        $search = $this->lastRequest[$target->getName()];
        if($this->getServer()->getPlayer($search) !== null){
            return $this->getServer()->getPlayer($search);
        }
        return $search;
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function hasOutgoingRequest(Player $player): bool{
        if(in_array($player->getName(), array_keys($this->TPRequests))){
            return true;
        }
        return false;
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function hasIncomingRequest(Player $player): bool{
        foreach(array_values($this->TPRequests) as $request){
            if(in_array($player->getName(), $request)){
                return true;
            }
        }
        return false;
    }

    /**
     * @param Player $requester
     * @param Player $target
     * @return bool
     */
    public function hasIncomingRequestFrom(Player $requester, Player $target): bool{
        foreach($this->TPRequests[$requester->getName()] as $request){
            if(in_array($target->getName(), $request)){
                return true;
            }
        }
        return false;
    }

}
