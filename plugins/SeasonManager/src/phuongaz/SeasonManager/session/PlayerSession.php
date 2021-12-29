<?php

namespace phuongaz\SeasonManager\session;

use pocketmine\Player;
use phuongaz\SeasonManager\SeasonManager;
use phuongaz\SeasonManager\event\PlayerLevelUpEvent;
Class PlayerSession{

	private $level;
	
	private $exp;

	private $data;

	public function __construct(Player $player)
	{
		$this->data = yaml_parse_file(SeasonManager::getInstance()->getDataFolder(). "users.yml");
		$this->level = $this->data[$player->getName()]["level"];
		$this->exp = $this->data[$player->getName()]["exp"];
	}

	public function getLevel() :int 
	{
		return $this->level;
	}	

	public function getExp() :int 
	{
		return $this->exp;
	}

	public function getNextExp() :int 
	{
		return $this->level *100;
	}

	public function addExp(int $exp = 1) :void 
	{
		$this->exp += $exp;
		if($this->exp >= $this->getNextExp()){
			$this->addLevel();
			$this->exp = 0;
			$event = new PlayerLevelUpEvent($this->getPlayer(), $this->getLevel());
			SeasonManager::getInstance()->getServer()->getPluginManager()->callEvent($event);
		}
	}

	public function addLevel(int $level = 1): void 
	{
		$this->level += $level;
	}

	public function emit() :void 
	{
		yaml_emit_file(SeasonManager::getInstance()->getDataFolder(). "users.yml", $this->data);
	}
}