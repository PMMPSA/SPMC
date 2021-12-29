<?php

namespace phuongaz\LOCMCore\EventHandler;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use phuongaz\LOCMCore\Loader;

class AFKHandler implements Listener {

    public function onJoin(PlayerJoinEvent $event): void
    {
        Loader::$times[$event->getPlayer()->getName()] = time();
    }

    public function onQuit(PlayerQuitEvent $event): void
    {
        unset(Loader::$times[$event->getPlayer()->getName()]);
    }

    public function onMove(PlayerMoveEvent $event): void
    {
        if($event->getFrom() === $event->getTo()) return;
        Loader::$times[$event->getPlayer()->getName()] = time();
    }

}