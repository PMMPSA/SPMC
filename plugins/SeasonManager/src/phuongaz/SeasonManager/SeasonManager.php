<?php

namespace phuongaz\SeasonManager;

use pocketmine\Sever;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

use phuongaz\SeasonManager\session\PlayerSession;

Class SeasonManager extends PluginBase
{
	CONST CURRENT_SEASON = 2;

	private static $instance;

	public function onEnable() :void 
	{
		self::$instance = $this;
		$this->saveResource("users.yml");
		Server::getInstance()->getPluginManager()->registerEvents(new EventListener(), $this);
	}

	public function getPlayerSession(Player $player) :PlayerSession
	{
		return new PlayerSession($player);
	}

	public static function getInstance() :self 
	{
		return self::$instance;
	}

	public static function getCurrentSeason() :int 
	{
		return self::CURRENT_SEASON;
	}
}