<?php

namespace phuongaz\LOCMCore;

use libs\muqsit\chunkloader\ChunkRegion;
use pocketmine\Player;
use pocketmine\level\Position;
use pocketmine\plugin\PluginBase;
use pocketmine\entity\Entity;
use pocketmine\utils\Config;
use pocketmine\item\enchantment\{
	Enchantment,
	EnchantmentInstance
};
use pocketmine\item\{Item, Armor, Tool};
use pocketmine\Server;

use phuongaz\LOCMCore\Manager\PlayerManager;
use phuongaz\LOCMCore\Manager\ScoreManager;
use phuongaz\LOCMCore\Manager\SkinManager;
use phuongaz\LOCMCore\Manager\RankManager;
use phuongaz\LOCMCore\Manager\DungeonManager;
use phuongaz\LOCMCore\Manager\MusicManager;

use pocketmine\scheduler\ClosureTask;
use phuongaz\LOCMCore\Task\ScorebroadTask;
use phuongaz\LOCMCore\Task\ClearLagTask;
use phuongaz\LOCMCore\Task\BroadcastTask;
use phuongaz\LOCMCore\Task\RankTask;
use phuongaz\LOCMCore\Task\AFKTask;
use phuongaz\LOCMCore\Task\DungeonTask;

use phuongaz\LOCMCore\Command\HubCommand;
use phuongaz\LOCMCore\Command\CItemCommand;
use phuongaz\LOCMCore\Command\TextCommand;
use phuongaz\LOCMCore\Command\RankCommand;
use phuongaz\LOCMCore\Command\DungeonCommand;
use phuongaz\LOCMCore\Command\EnchantCommand;
use phuongaz\LOCMCore\Command\FixCommand;
use phuongaz\LOCMCore\Command\FunctionCommand;
use phuongaz\LOCMCore\Command\PvpCommand;
use phuongaz\LOCMCore\Command\OffMemCommand;

use phuongaz\LOCMCore\EventHandler\AFKHandler;
use phuongaz\LOCMCore\EventHandler\RewardHandler;

use phuongaz\LOCMCore\Entity\TextEntity;

use pocketmine\network\mcpe\protocol\types\SkinAdapterSingleton;

use onebone\economyapi\EconomyAPI;

Class Loader extends PluginBase{

	public static $isoff = false;

	private static $instance;
	private static $devs = [];
	public static $enchantments = [];
	public static $lastUpload;
    public static $lastDownload;
    private $originalAdaptor = null;

    private $rank;

    public static $times = [];

	public function onEnable(){

		date_default_timezone_set("Asia/Ho_Chi_Minh");
		$this->getServer()->getNetwork()->setName("§l§eＳＰＭＣ§a ＳＫＹＢＬＯＣＫ");
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
		foreach (array_diff(scandir($this->getServer()->getDataPath() . "worlds"), ["..", "."]) as $levelName) {
            $this->getServer()->loadLevel($levelName);
        }
        $this->saveResource("contents.yml");
        $this->saveResource("function.yml");
        self::$instance = $this;
        $pos = $this->getServer()->getDefaultLevel()->getSafeSpawn();
		$this->loadChunk($pos);
		$this->runTasks();
		$this->runTasks();
		$this->loadConfig();
		$this->registerCommands();
		$this->setEnchants();
		$this->registerEvents();
		Entity::registerEntity(TextEntity::class, true);
        $this->originalAdaptor = SkinAdapterSingleton::get();
        SkinAdapterSingleton::set(new ParsonaSkinAdapter);
	}

	public function onDisable(){
        if($this->originalAdaptor !== null){
            SkinAdapterSingleton::set($this->originalAdaptor);
        }
	}

	public function getJoinContents() :array{
		return yaml_parse_file($this->getDataFolder(). "contents.yml");
	}

	public function getFunctionData() :array{
		return yaml_parse_file($this->getDataFolder(). "function.yml");
	}

	public function registerEvents() :void{
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
		$this->getServer()->getPluginManager()->registerEvents(new AFKHandler($this), $this);
		//$this->getServer()->getPluginManager()->registerEvents(new RewardHandler($this), $this);
	}

	public function loadConfig(){
		$this->saveResource("skins/text.png");
		$this->saveResource("logs/game.log");
		$this->saveResource("skins/text.json");	
		$this->rank = new Config($this->getDataFolder(). "ranks.yml", Config::YAML);	
	}

	public function setEnchants(){
		for($i = 0; $i <= 30; $i++){
			if($i == 16) continue;
			if($i == 26) continue;
			if(($enchant = Enchantment::getEnchantment((int)$i)) instanceof Enchantment){
				self::$enchantments[] = $enchant;
			}
		}
	}

	public function EnchantItem(Player $player, Enchantment $enchant, int $level = 1) :bool{
		$enchantment = new EnchantmentInstance($enchant, $level);
		$item = $player->getInventory()->getItemInHand();
		if($item->getId() != Item::AIR || $item instanceof Tool || $item instanceof Armor){
			$item->addEnchantment($enchantment);
			EconomyAPI::getInstance()->reduceMoney($player, $level*10000);
			//$this->getEco()->reduceMoney($player, $level * self::$money);
			$player->getInventory()->setItemInHand($item);
			return true;
		}
		return false;
	}

	public function registerCommands(){
		//$this->getServer()->getCommandMap()->register("hub", new HubCommand());

		$this->getServer()->getCommandMap()->register("text", new TextCommand());
		$this->getServer()->getCommandMap()->register("offmem", new OffMemCommand());
		$this->getServer()->getCommandMap()->register("rank", new RankCommand());
		$this->getServer()->getCommandMap()->register("ec", new EnchantCommand());
		$this->getServer()->getCommandMap()->register("dungeon", new DungeonCommand());
		$this->getServer()->getCommandMap()->register("fix", new FixCommand());
		$this->getServer()->getCommandMap()->register("pvp", new PvpCommand());
		$this->getServer()->getCommandMap()->register("citem", new CItemCommand());
		$this->getServer()->getCommandMap()->register("function", new FunctionCommand());
	}

	public function runTasks(){
		$this->getScheduler()->scheduleRepeatingTask(new ScorebroadTask(), 20*15);
		$this->getScheduler()->scheduleRepeatingTask(new DungeonTask(), 20*60*30);
		$this->getScheduler()->scheduleRepeatingTask(new ClearLagTask(), 20);
		$this->getScheduler()->scheduleRepeatingTask(new RankTask(), 20);
		$this->getScheduler()->scheduleRepeatingTask(new BroadcastTask(), 2000);
       // $this->getScheduler()->scheduleRepeatingTask(new AFKTask($this), 20 * 60);
        $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(
            function (int $currentTick): void {
                $s = $this->getServer();
                $u = $s->getNetwork()->getUpload();
                $d = $s->getNetwork()->getDownload();
                if ($u !== 0) Loader::$lastUpload = round(($u / 1000), 1);
                if ($d !== 0) Loader::$lastDownload = round(($d / 1000), 1);
            }
        ), 5);
	}

	public function log(string $content){
        $file = $this->getDataFolder(). "logs/game.log";
        $fh = fopen($file,"a") or die("cant open file");
        fwrite($fh,$content);
        fwrite($fh,"\r\n");
        fclose($fh);
	}

	public static function getInstance(){
		return self::$instance;
	}

	public function getPlayerManager(Player $player) :PlayerManager{
		return new PlayerManager($player);
	}

	public function getScoreManager() :ScoreManager{
		return new ScoreManager();
	}

	public function getSkinManager() :SkinManager{
		return new SkinManager();
	}

	public function getDungeonManager() :DungeonManager{
		return new DungeonManager();
	}

	public function getRankManager() :RankManager{
		return new RankManager($this->rank);
	}

	public function getMusicManager() :MusicManager{
		return new MusicManager();
	}

	public function loadChunk(Position $pos){
		ChunkRegion::onChunkGenerated($pos->getLevel(), $pos->getX() >> 16, $pos->getZ() >> 16, function() use($pos){
		});
	}

	public function addDev(Player $player){
		self::$devs[strtolower($player->getName())] = true;
	}

	public function isDev(Player $player) :bool{
		if(in_array(strtolower($player->getName()), self::$devs)){
			return true;
		}
		return false;
	}

	public static function broadcast(string $content){
		foreach(Server::getInstance()->getOnlinePlayers() as $player){
			$player->sendMessage($content);
		}
	}
}