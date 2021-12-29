<?php

namespace phuongaz\coin;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Server;

class EventListener implements Listener
{

    public function onJoin(PlayerJoinEvent $event)
    {
        $player = $event->getPlayer();
        if (!isset(Coin::$coin[strtolower($player->getName())])) {
            Coin::$coin[strtolower($player->getName())] = Coin::$setting->get("default-coin");
        }
    }
}