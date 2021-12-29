<?php

namespace Itzdvbravo\BravoClan\Forms\SubForms;

use pocketmine\Player;

use jojoe77777\FormAPI\CustomForm;
use Itzdvbravo\BravoClan\Main;
use Itzdvbravo\BravoClan\Forms\ClanForm;

Class TopSubForm extends CustomForm{

	/** @var Player */
	private $player;

	public function __construct(Player $player){
		$this->player = $player;
		$callable = $this->getCallable();
		parent::__construct($callable);
	}

	/**
	* @return void
	*/
	public function setForm() :void{
        $top = Main::$db->query("SELECT clan FROM clans ORDER BY level DESC LIMIT 10");
        $counter = 0;
        $this->addLabel("§l§6TOP§e 10§6 CLAN IN SERVER");
        while ($resultAr = $top->fetchArray(SQLITE3_ASSOC)) {
            $counter += 1;
            $clan = Main::$file->getClan($resultAr['clan']);
            $this->addLabel("§l§cＴＯＰ §e{$counter}. §fCLAN (§e{$clan['clan']}§r§l§f) §fLEVEL §f(§e{$clan['level']}§f)");
        }
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