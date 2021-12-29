<?php

namespace phuongaz\LOCMCore\Manager;

use pocketmine\Player;
use pocketmine\level\Position;
use libs\muqsit\chunkloader\ChunkRegion;

Class PlayerManager{

	/** @var Player */
	private $player;

	public function __construct(Player $player){
		$this->player = $player;
	}

	/**
	* @return Player
	*/
	public function getPlayer() :?Player{
		return $this->player;
	}

	/**
	* @param Position $position
	*
	* @return void
	*/
	public function teleport(Position $position) :void{
		$player = $this->getPlayer();
		ChunkRegion::onChunkGenerated($player->getLevel(), $position->getX() >> 4, $player->getZ() >> 4, function() use($position){
		    $this->getPlayer()->teleport($position);
		});
	}
}