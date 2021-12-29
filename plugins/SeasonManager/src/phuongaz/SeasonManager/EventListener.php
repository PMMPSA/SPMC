<?php

namespace phuongaz\SeasonManager;

use pocketmine\Server;
use pocketmine\Player;
use phuongaz\SeasonManager\event\PlayerLevelUpEvent;
use pocketmine\event\Listener;

Class EventListener implements Listener
{
	public function onLevelup(PlayerLevelUpEvent $event) :void 
	{
		$player = $event->getPlayer();
		$level = $event->getLevel();
	}
}