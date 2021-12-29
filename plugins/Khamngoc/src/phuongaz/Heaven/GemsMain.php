<?php

namespace phuongaz\Heaven;

use pocketmine\{
	Server,
	Player
};
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\item\Item;
use pocketmine\item\enchantment\{
	Enchantment,
	EnchantmentInstance
};
use pocketmine\nbt\tag\{
	StringTag,
	IntTag
};

use jojoe77777\FormAPI\{
	SimpleForm,
	CustomForm
};

use phuongaz\Heaven\Commands\GemsCommands;
use phuongaz\Heaven\Commands\ChanceGemsCommand;
use phuongaz\Heaven\Events\EventListeners;

use DaPigGuy\PiggyCustomEnchants\CustomEnchantManager;

Class GemsMain extends PluginBase{

	private $config;

	public function onEnable() :void
	{
		$this->saveDefaultConfig();
		$this->getServer()->getPluginManager()->registerEvents(new EventListeners($this), $this);
		$this->getServer()->getCommandMap()->register('gems', new GemsCommands($this));
		$this->getServer()->getCommandMap()->register('chancegem', new ChanceGemsCommand($this));
	}

	public function getConfig() :Config{
		return new Config($this->getDataFolder(). 'Gems.yml', Config::YAML);
	}

	public function checkGem(int $id) :bool{
		$config = $this->getConfig()->getAll();
		if(array_key_exists($id, $config)){ 
			return true; 
		}
		 return false;
	}

	
	public function getGems(int $id) :Item{
		$config = $this->getConfig();
		if($this->checkGem($id)){
			$i = $config->get($id)['ID'];
			$ex = explode(':', $i);
			$items = Item::get($ex[0], $ex[1], 1);
			$item = $this->addEnchantToGem($items, $id);
			//$item->setCustomName((string)$config->get($id)['Name']);
			$item->setLore(array($config->get($id)['Lore']));
		}else{
			$this->sendError($id, 'DATA ITEM NOT EXIST');
			$item = Item::get(Item::AIR);
		}
		return $item;
	}


	public function setNBT(Item $item, int $id, int $level) :Item{
		$item->setNamedTagEntry(new StringTag('Gems', "YES"));
		$item->setNamedTagEntry(new IntTag('ID', $id));
		$nbt = $item->getNamedTag();
		$nbt->setInt("Level", $level);
		$item->setNamedTag($nbt);
		$item->setNamedTagEntry(new IntTag('Enchant', $id));
		$item->setNamedTagEntry(new IntTag('Level', $level));
		return $item;
	}

	private function sendError(int $id, string $error) :void{
		$this->getLogger()->error('Data config error: '. $id);
		$this->getLogger()->error('ERROR: '. $error);
	}

	public function sendGemsForm( $player) {
		$form = new SimpleForm(function(Player $player, $data){
			if(is_null($data)) return;
			$this->sendCustomForm($player, $data);
		});
		$config = $this->getConfig()->getAll();
		$form->setTitle('GEMS SYSTEM');
		$form->addButton('EXIT');
		foreach (array_keys($config) as $key) {
			$name = $this->getConfig()->get($key)["Name"];
			$form->addButton((string)$name);
		}
		$form->sendToPlayer($player);
		
	}

	public function sendCustomForm($player, $id){
		$form = new CustomForm(function(Player $player, $data) use ($id){
			if(is_null($data)) return;
			$gem = $this->getGems($id);
			$player->getInventory()->addItem($gem);
		});
		$form->setTitle('INFO GEMS');
		$config = $this->getConfig()->get($id);
		$form->addLabel('Name gem: '. $config['Name']);
		$form->addLabel('Item: '. $this->getGems($id)->getName());
		$form->addLabel($config['Lore']);
		$form->sendToPlayer($player);
	}

	public function addEnchantToGem(Item $gem, int $id) {
		$data = $this->getConfig()->get($id)["Enchants"];
//		var_dump($data); //for debug
		$item = Item::get(0);
		if(!empty(($ces = $data["PiggyCE"]))){
			$ces = explode(":", $ces);
			$lv = mt_rand(1,3);
			if(($ce = CustomEnchantManager::getEnchantment((int)$ces[0])) != null){
				$item = $this->setNBT($gem, (int)$ces[0], $lv);
				$enchantment = new EnchantmentInstance($ce, $lv);
				$item->addEnchantment($enchantment);
			}
		}
		if(!empty(($ecs = $data["VanillaEC"]))){
			$e = explode(":", $ecs);
			$lv = mt_rand(5,8);
			if(in_array($e[0], [13, 18])){
				$level = mt_rand(1,3);
			}
			if(($ec = Enchantment::getEnchantment((int)$e[0])) instanceof Enchantment){
				$item = $this->setNBT($gem, (int)$e[0], $lv);
				//var_dump($item);
				$enchantment = new EnchantmentInstance($ec, $lv);
				$item->addEnchantment($enchantment);
			}
		}
		$level = $item->getNamedTag()->getTag("Level", IntTag::class)->getValue();
		$item->setCustomName($this->getConfig()->get($id)["Name"]. " §l§f【§aLEVEL§e ".$lv."§f】");
		return $item;
	}
}