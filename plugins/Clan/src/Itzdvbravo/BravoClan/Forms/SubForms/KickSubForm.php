<?php

namespace Itzdvbravo\BravoClan\Forms\SubForms;

use pocketmine\Player;
use Itzdvbravo\BravoClan\Forms\ClanForm;
use Itzdvbravo\BravoClan\Main;
use onebone\economyapi\EconomyAPI;
use jojoe77777\FormAPI\CustomForm;
use pocketmine\Server;
Class KickSubForm extends CustomForm{

	/*** @var Player*/
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
	public function setForm() :void {
		$player = $this->player;
		$this->setTitle("§l§eKick Form");
		$this->addInput("§l§e⇁ §fMember name:");
		$clan = Main::$file->getClan(Main::$clan->player[strtolower($player->getName())]);
		$m = Main::$file->clanMembers($clan['clan']);
        foreach ($m as $person){
            $info = Main::$file->getMember($person);
            if (Main::getInstance()->isOnline($person)){
                $this->addLabel("§e§l⇁§f {$person} §c(§f{$info['kills']}§e/§f{$info['deaths']}§c)§f [§aON§f]");
            } else {
                $this->addLabel("§e§l⇁§f {$person} §c(§f{$info['kills']}§e/§f{$info['deaths']}§c)§f [§4OFF§f]");
            }
        }
	}

	/**
	* @return callable
	*/
	public function getCallable() :callable{
		$callable = function(Player $player, ?array $data){
			if(is_null($data[0])){
				$form = new ClanForm($player);
				$form->setForm();
				$player->sendForm($form);
				return;
			}
			Server::getInstance()->getCommandMap()->dispatch($player, "clan kick ". $data[0]);
		};
		return $callable;
	}
}