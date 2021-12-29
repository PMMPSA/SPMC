<?php

namespace phuongaz\LOCMCore\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use phuongaz\LOCMCore\Loader;
use pocketmine\Player;
use pocketmine\entity\Entity;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\StringTag;

use phuongaz\LOCMCore\Entity\TextEntity;

Class TextCommand extends Command{

	private $loader;

	private $blocks = [];

	public function __construct(){
		parent::__construct("text", ".");
	}

	public function execute(CommandSender $sender, string $label, array $args):bool{
		if($sender instanceof Player and $sender->getName() == "phuongaz"){
			$this->blocks[$sender->getInventory()->getItemInHand()->getId()] = $sender->getInventory()->getItemInHand();
			$nbt = Entity::createBaseNBT($sender, null, $sender->yaw);
			$nbt->setTag(new CompoundTag("Skin", [
				"Data" => new StringTag("Data", Loader::getInstance()->getSkinManager()->getSkin("text")->getSkinData()),
				"Name" => new StringTag("Name", "Text")
			]));
	        $entity = new TextEntity($sender->getLevel(), $nbt);
	        $entity->setSkin(Loader::getInstance()->getSkinManager()->getSkin("text"));
		    $entity->spawnToAll();
		    $entity->setScale(3);
		}
/*		if(isset($args[0])){
			$text = '';
			foreach($this->blocks as $id => $item){

				$text .= $item->getId().":".$item->getDamage().":64:giamua:giasell:Default:texture\n";
			}
			yaml_emit_file(Loader::getInstance()->getDataFolder(). "shop.yml", $text);
		}*/
		return true;
	}
}