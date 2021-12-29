<?php

namespace phuongaz\SeasonManager\event;

use pocketmine\Player;
use pocketmine\event\player\PlayerEvent;
use pocketmine\event\Cancellable;

Class PlayerLevelUpEvent extends PlayerEvent implements Cancellable
{
	protected $player;

	private $level;

	public function __construct(Player $player, int $level)
	{
		$this->player = $player;
		$this->level = $level;
	} 

	public function getPlayer() :Player 
	{
		return $this->player;
	}

	public function getLevel() :int 
	{
		return $this->level;
	}
}