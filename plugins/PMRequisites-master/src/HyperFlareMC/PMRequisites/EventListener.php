<?php

declare(strict_types=1);

namespace HyperFlareMC\PMRequisites;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;

class EventListener implements Listener{

    /**
     * @var Main
     */
    private $plugin;

    public function __construct(Main $plugin){
        $this->plugin = $plugin;
    }

    public function onLogin(PlayerLoginEvent $event){
        $player = $event->getPlayer();
        $player->getServer()->getCommandMap()->dispatch($player, "spawn");
    }

}