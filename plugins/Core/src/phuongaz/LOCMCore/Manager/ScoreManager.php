<?php

namespace phuongaz\LOCMCore\Manager;

use JackMD\ScoreFactory\ScoreFactory;
use pocketmine\Player;
use pocketmine\entity\Living;
use pocketmine\Server;
use pocketmine\utils\Utils;
use phuongaz\LOCMCore\Loader;
use onebone\economyapi\EconomyAPI;
use phuongaz\Coin\Coin;
Class ScoreManager{

	public function addScore(Player $p, string $type = "Member")
    {
    	ScoreFactory::setScore($p, "§l§eＳＰＭＣ§a ＳＫＹＢＬＯＣＫ");
        $data = $this->getStats($type);
        if($type == "Member"){
        	ScoreFactory::setScoreLine($p, 1 , "§l§e→ §fＮgười chơi:§a ".$p->getName());
        	ScoreFactory::setScoreLine($p, 2 , "§l§e→ §fLcoin:§a ".Coin::getInstance()->getCoin($p));
        	ScoreFactory::setScoreLine($p, 3,  "§l§e→ §fXu:§a ".EconomyAPI::getInstance()->myMoney($p));
        	ScoreFactory::setScoreLine($p, 4 , "§l§e→ §fTrực tuyến:§a ". count(Server::getInstance()->getOnlinePlayers())."§f/§a25");
        	ScoreFactory::setScoreLine($p, 5 , "§l§e→ §fＰing:§a ".$p->getPing());
        	ScoreFactory::setScoreLine($p, 6 , "      §espmc.servegame.com");

        }elseif($type == "Developer"){
	        ScoreFactory::setScoreLine($p, 1, "§cPing: §f" . $p->getPing() . " ms");
	        ScoreFactory::setScoreLine($p, 2, "§6Load: §f$data[Load]%%");
	        ScoreFactory::setScoreLine($p, 3, "§eUpload: §f$data[NetworkUpload] KB/s");
	        ScoreFactory::setScoreLine($p, 4, "§aDownload: §f$data[NetworkDownload] KB/s");
	        ScoreFactory::setScoreLine($p, 5, "§bTPS: §f$data[TPS]");
	        ScoreFactory::setScoreLine($p, 6, "§dMemory In Use: §f$data[InUseMemory] MB ");
	        ScoreFactory::setScoreLine($p, 7, "§cTotal Memory: §f$data[TotalMemoryAvailable] MB ");
	        ScoreFactory::setScoreLine($p, 8, "§6Levels Loaded: §f$data[LoadedLevels]");
	        ScoreFactory::setScoreLine($p, 9, "§eOnline: §f$data[OnlinePlayers]");
	        ScoreFactory::setScoreLine($p, 10, "§aEntities: §f$data[Entites]");
	        ScoreFactory::setScoreLine($p, 11, "§bLive Entities: §f$data[LiveEntites]");        	
        }

    }

    public function getStats(string $type = "Member"): array
    {
    	if($type == "Member"){
    		$data = [];
    	}elseif($type == "Developer"){
	        $s = Server::getInstance();
	        $lvls = 0;
	        $entities = 0;
	        $liveentities = 0;
	        $memory = Utils::getMemoryUsage(true);
	        foreach (Server::getInstance()->getLevels() as $level) {
	            foreach ($level->getEntities() as $ent) {
	                $entities++;
	                if ($ent instanceof Living) {
	                    $liveentities++;
	                }
	            }
	        }
	        foreach ($s->getLevels() as $l) {
	            $lvls++;
	        }
	        $u = $s->getNetwork()->getUpload();
	        $d = $s->getNetwork()->getDownload();
	        if ($u !== 0) {
	            $upload = round(($u / 1000), 1);
	        } else $upload = Loader::$lastUpload;
	        if ($d !== 0) {
	            $download = round(($d / 1000), 1);
	        } else $download = Loader::$lastDownload;
	        $data = [
	            "Load" => $s->getTickUsage(),
	            "TPS" => $s->getTicksPerSecond(),
	            "NetworkUpload" => $upload,
	            "NetworkDownload" => $download,
	            "InUseMemory" => (round($memory[0] / 1000000)),
	            "TotalMemoryAvailable" => (round($memory[2] / 1000000)),
	            "LoadedLevels" => $lvls,
	            "OnlinePlayers" => count($s->getOnlinePlayers()),
	            "Entites" => $entities,
	            "LiveEntites" => $liveentities
	        ];    		
    	}
        return $data;
    }
}