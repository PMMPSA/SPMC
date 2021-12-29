<?php

/*
 * EconomyS, the massive economy plugin with many features for PocketMine-MP
 * Copyright (C) 2013-2016  onebone <jyc00410@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace onebone\economyapi\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\Player;
use onebone\economyapi\EconomyAPI;

class SeeMoneyCommand extends Command{
	private $plugin;

	public function __construct(EconomyAPI $plugin){
		parent::__construct("seemoney", "xem tien cua nguoi choi khac.", "/seemoney [player]", ["xemxu", "seemoney"]);
		$this->setPermission("economyapi.command.seemoney");

		$this->plugin = $plugin;
	}

	public function execute(CommandSender $sender, string $label, array $params) : bool{
		if(!$sender->hasPermission($this->getPermission())){
			$sender->sendMessage(EconomyAPI::$prefix . ".");
			return true;
		}

		$player = array_shift($params);

		if(trim($player) === ""){
			$sender->sendMessage(EconomyAPI::$prefix . "Vui lòng nhập tên người chơi : " . $this->getUsage());
			return true;
		}

		if(($p = $this->plugin->getServer()->getPlayer($player)) instanceof Player){
			$player = $p->getName();
		}

		if(!$this->plugin->accountExists($player)){
			$sender->sendMessage(EconomyAPI::$prefix ." §l§fNgười chơi§e ". $player . " §fkhông tồn tại.");
			return true;
		}
		$total = count($this->plugin->getAllMoney());
		$sender->sendMessage(EconomyAPI::$prefix ."Xu của người chơi§e $player §fhiện có:§e " . $this->plugin->koreanWonFormat($this->plugin->myMoney($player)));
		//$sender->sendMessage(EconomyAPI::$prefix . $player . "님의 순위 : 전체 " . number_format($total) . "명중 " . number_format($this->plugin->getRank($player)) . "위");
		return true;
	}
}
