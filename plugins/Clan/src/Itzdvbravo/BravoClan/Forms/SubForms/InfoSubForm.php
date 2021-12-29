<?php

namespace Itzdvbravo\BravoClan\Forms\SubForms;

use pocketmine\Player;

use jojoe77777\FormAPI\CustomForm;
use Itzdvbravo\BravoClan\Forms\ClanForm;
use Itzdvbravo\BravoClan\Main;

Class InfoSubForm extends CustomForm{

	/** @var Player */
	private $player;

	public function __construct(Player $player){
		$this->player = $player;
		$this->setForm();
		$callable = $this->getCallable();
		parent::__construct($callable);
	}

	/**
	* @return void
	*/
	public function setForm() :void{
		$player = $this->player;
		$this->setTitle("Info clan");
		$clan = Main::$file->getClan(Main::$clan->player[strtolower($player->getName())]);
        $this->addLabel("§l§e→§f Leader:§f {$clan['leader']}");
        $this->addLabel("§l§e→§f Members:§e {$clan['tm']}§f/§e{$clan['maxtm']}");
        $this->addLabel("§l§e→§f Level:§e {$clan['level']}");
        $this->addLabel("§l§e→§f XP:§e {$clan['xp']}/{$clan['nex']}");
        $this->addLabel("§l§e→§f KDR:§e {$clan['kills']}§f/§e{$clan['deaths']}");
	}

	/**
	* @return callable
	*/
	public function getCallable() :callable{
		$callable = function(Player $player, ?array $data){ 
			$form = new ClanForm($player);
			$form->setForm();
			$player->sendForm($form);
		};
		return $callable;
	}
}