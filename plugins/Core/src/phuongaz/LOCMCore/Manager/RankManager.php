<?php


namespace phuongaz\LOCMCore\Manager;

use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\Server;

Class RankManager{

	/** @var Config*/
	private $config;

	public function __construct(Config $config){
		$this->config = $config;
	}

	/**
	* @return Config;
	*/
	public function getConfig() :Config{
		return $this->config;
	}

	public function getPP(){
		$pp = Server::getInstance()->getPluginManager()->getPlugin("PurePerms");
		if($pp == null){
			Server::getLogger()->warning("Chua cai PurePerms");
		}
		return $pp;
	}

	/**
	* @param Player $player
	* @param string $rank
	* @param int $day
	*
	* @return bool
	*/
	public function addRank(Player $player, string $rank, int $day = 1) :bool{
		$config = $this->getConfig();
		$lowname = strtolower($player->getName());
		if($config->exists($lowname) and $config->get($lowname)["RANK"] == $rank){
			$current = $config->get($lowname)["DAY"];
			$new = $current + ($day * 86400);
			$config->set($lowname, ["DAY" => $new, "RANK" => $rank]);
		}elseif(!$config->exists($lowname)){
			$group = $this->getPP()->getGroup($rank);
			if($group == null) $group = $this->getPP()->getDefaultGroup();
			$this->getPP()->setGroup($player, $group);
			$time = time() + $day * 86400;
			$config->set($lowname, ["DAY" => $time, "RANK" => $rank]);
		}else{
			return false;
		}
		$config->save();
		return true;
	}

	public function checkRank(Player $player, string $rank){
		$config = $this->getConfig();
		$lowname = strtolower($player->getName());
		if($config->exists($lowname) and $config->get($lowname)["RANK"] == $rank){
			return true;
		}elseif(!$config->exists($lowname)){
			return null;
		}elseif($config->exists($lowname) and $config->get($lowname)["RANK"] != $rank){
			return false;
		}
	}

	/**
	* @param Player $player
	*
	* @return int|null
	*/

	public function getDay(Player $player){
		$config = $this->getConfig();
		$lowname = strtolower($player->getName());
		if($config->exists($lowname)){
			return $config->get($lowname)["DAY"];
		}
		return null;
	}

	/**
	* @param string $key
	*/
	public function removeRank(string $key){
		$key = strtolower($key);
		$config = $this->getConfig();
		if($config->exists($key)){
/*			$all = $config->getAll();
			unset($all[$key]);
			$config->set($all)*/
			$config->remove($key);
			$config->save();
		}
	}
    /*
	Check config and remove outdate rank
    */
	public function Handling(){
		foreach(Server::getInstance()->getOnlinePlayers() as $player){
			$all = $this->config->getAll();			
			foreach(array_keys($all) as $key){
				$data = $this->config->get($key);
				$pday = $data["DAY"];
				$ndate = strtotime("now");
				$day = round(($pday - $ndate) / 86400);
				if($day < 0){
					$player->sendMessage("§l§e→ §fRank của bạn đã hết hạn");
					$this->getPP()->setGroup($player, $this->getPP()->getDefaultGroup());
					$this->removeRank($key);
				}
			}			
		}
	}
}