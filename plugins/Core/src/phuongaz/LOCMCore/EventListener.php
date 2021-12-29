<?php

namespace phuongaz\LOCMCore;

use pocketmine\event\player\{
	PlayerJoinEvent,
	PlayerDeathEvent,
	PlayerLoginEvent,
	PlayerRespawnEvent,
	PlayerMoveEvent,
	PlayerCommandPreprocessEvent,
    PlayerCreationEvent
};
use pocketmine\event\block\{BlockUpdateEvent, BlockPlanceEvent};

use pocketmine\event\Listener;
use pocketmine\event\entity\{
	EntityLevelChangeEvent
};
use bossbar_system\models\BossBar;
use pocketmine\block\Block;
use pocketmine\block\IronOre;
use pocketmine\block\Cobblestone;
use pocketmine\block\Glowingobsidian;
use pocketmine\block\DiamondOre;
use pocketmine\block\EmeraldOre;
use pocketmine\block\GoldOre;
use pocketmine\block\CoalOre;
use pocketmine\block\LapisOre;
use pocketmine\block\RedstoneOre;
use pocketmine\block\Water;
use pocketmine\block\Stonecutter;

use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

use pocketmine\event\entity\EntityDeathEvent;

use jojoe77777\FormAPI\{SimpleForm, CustomForm};

Class EventListener implements Listener{

	/** @var Loader */
	private $loader;

	public function __construct(Loader $loader){
		$this->loader = $loader;
	}

/*    public function onEntityDeath(EntityDeathEvent $event) :void {
        var_dump($event->getEntity());
    }
*/
	/** 
	* @return Loader
	*/
	public function getLoader() :Loader{
		return $this->loader;
	}

	public function onDeath(PlayerDeathEvent $event){
		$event->setKeepInventory(true);
	}

	public function c(PlayerJoinEvent $event) :void {
		$player = $event->getPlayer();
		$loader = $this->getLoader();
/*		$bossbar = new BossBar("§l§6Ｌｅｇｅｎｄ Ｏｆ Ｃｒａｆｔ Ｍａｓｔｅｒ", 1.0);
		$bossbar->send($player);*/
	}

	public function onLogin(PlayerLoginEvent $event){
		$player = $event->getPlayer();
		$loader = $this->getLoader();
		$pos = $loader->getServer()->getDefaultLevel()->getSafeSpawn();
		$loader->loadChunk($pos);
		$player->teleport($pos);
	}

	public function onRespawn(PlayerRespawnEvent $event){
		$player = $event->getPlayer();
		$loader = $this->getLoader();
		$pos = $loader->getServer()->getDefaultLevel()->getSafeSpawn();
		$loader->loadChunk($pos);
		$event->setRespawnPosition($pos);
	}

    public function onPlayerCreation(PlayerCreationEvent $event){
        $event->setPlayerClass(CustomPlayer::class);
    }


	public function onMovement(PlayerMoveEvent $event) {
      $player = $event->getPlayer();
      $block = $player->getLevel()->getBlock($player->floor()->add(0, +1));
      $id = $block->getId();
      $level = $player->getLevel()->getName();
      $default = $this->getLoader()->getServer()->getDefaultLevel()->getName();
        if($id == Block::PORTAL and $level == $default) {
        	$time = time() + 5;
        	if($time >= time()){
        		$this->getLoader()->getServer()->getCommandMap()->dispatch($player, "is");
        	}
  		}
  		if($player->y <= 0){
            $loader = $this->getLoader();
			$pos = $loader->getServer()->getDefaultLevel()->getSafeSpawn();
			$loader->loadChunk($pos);
			$player->teleport($pos);
			$player->setHealth($player->getMaxHealth());
			$player->setFood(20);
  		}
	}


    public function onCommandProcess(PlayerCommandPreprocessEvent $e){
        $msg = $e->getMessage();
        if($msg[0] == "/"){
            Loader::getInstance()->log(date("H:i:s")." ". " | ".$e->getPlayer()->getName(). " | ". $msg);
            Server::getInstance()->getLogger()->info("".$e->getPlayer()->getName(). " | ". $msg);
        }
    }

    public function onJoin(PlayerJoinEvent $event) :void{
        $player = $event->getPlayer();
        /*OFF MEM*/
        if(Loader::$isoff and !$player->isOp()){
            $player->kick("§l§eMÁY CHỦ HIỆN ĐANG BẢO TRÌ\n§l§fVUI LÒNG QUAY LẠI SAU", false);
        }
        /*JOIN FORM*/
        $form = new SimpleForm(function($player, ?int $data){
            if(is_null($data)) return;
            if($data == 0) $this->changelogs($player);
            if($data == 1) $this->ClassicForm($player);
            if($data == 2) Server::getInstance()->getCommandMap()->dispatch($player, "function");
        });
        $form->setTitle("§l§6ＳＰＭＣ");
        $form->addButton("§f§l• §4Thông Tin Cập Nhật§f •", 1, "https://cdn0.iconfinder.com/data/icons/awecons-business-part2/24/Business_Add_new-512.png");
        $form->addButton("§f§l• §4Lưu Ý §f•", 1, "https://upload.wikimedia.org/wikipedia/commons/thumb/0/0f/Note_icon.svg/768px-Note_icon.svg.png");
        $form->addButton("§f§l• §4Tính năng nhỏ §f•\n§f(§1/function§f)", 1, "https://www.iconfinder.com/data/icons/the-data-visualisation-catalogue-search-icon-set/170/processes_and_methods-512.png");
        $form->sendToPlayer($event->getPlayer());
    }
    public function onBlockSet(BlockUpdateEvent $event){
        
    }
    public function ClassicForm($player) :void{
        $form = new CustomForm(function($player, ?array $data){
            if(is_null($data)) return;
        });
        $form->setTitle("§l§6LOCM");
        foreach(Loader::getInstance()->getJoinContents()["contents"] as $content){
            $form->addLabel($content);
        }
        $form->sendToPlayer($player);
    }

    public function changelogs($player) : void {
        $form = new CustomForm(function($player, ?array $data){
            if(is_null($data)) return;
        });
        $form->setTitle("§l§6LOCM");
        foreach(Loader::getInstance()->getJoinContents()["changelogs"] as $content){
            $content = str_replace("*", '%', $content);
            $form->addLabel($content);
        }
        $form->sendToPlayer($player); 
    }

/*    public function onJoin(PlayerJoinEvent $event)
    {
        $closure = function (int $currentTick): void {
            $selected = next(self::$strings);
            if ($selected === false) {
                $selected = reset(self::$strings);
            }
            foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
                $str = TextFormat::RESET.str_replace(["0", "1", "2"], [self::EMPTY, self::HALF, self::FULL], str_pad($selected, 10, "0")) . self::RIGHT;
                $message = TextFormat::BOLD.TextFormat::DARK_RED."Mana\n$str\n";
                $message .= TextFormat::BOLD.TextFormat::BLUE."Water power\n \u{E0E9} \n";
                //$message .= TextFormat::BOLD.TextFormat::DARK_PURPLE."Psychic Remote Help power\n$str\n";
                $onlinePlayer->sendPopup(($message, 'UTF-8'));
            }
        };
        /** @var Closure $closure         Loader::getInstance()->getScheduler()->scheduleDelayedRepeatingTask(new ClosureTask($closure), 20, 1);
    }*/

/*	public function onChangeLevel(EntityLevelChangeEvent $event){
		$level = $entity->getTarget();
		$spawn = $level->getSafeSpawn();
		$this->getLoader()->loadChunk($spawn);
	}*/
}