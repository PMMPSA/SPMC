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
use onebone\economyapi\EconomyAPI;

class TopMoneyCommand extends Command{

	private $plugin;

	public function __construct(EconomyAPI $plugin){
		parent::__construct("topmoney", "bxh xu.", "/topmoney [page]", ["topmoney", "topxu"]);
		$this->setPermission("economyapi.command.topmoney");

		$this->plugin = $plugin;
	}

	public function execute(CommandSender $sender, string $label, array $params) : bool{
		if(!$sender->hasPermission($this->getPermission())){
			$sender->sendMessage(EconomyAPI::$prefix . "NO have permission.");
			return true;
		}

		$page = (int)array_shift($params);

		$max = count($this->plugin->getAllMoney());
		$maxPage = ceil($max / 5);
		$page = min($maxPage, $page);
		$page = max(1, $page);

		$server = $this->plugin->getServer();

		$ops = [];
		$banned = [];
		foreach($server->getNameBans()->getEntries() as $entry){
			$banned[strtolower($entry->getName())] = true;
		}

		foreach($server->getOps()->getAll() as $op => $tmp){
			$ops[strtolower($op)] = true;
		}

		$sender->sendMessage("§l§bBXH xu §a(§f" . $page . "§e/§f" . $maxPage . "§a)");
		for($i = 1; $i <= 5; $i++){
			$rank = (5 * ($page - 1)) + $i;
			if($rank > $max){
				break;
			}
			$player = $this->plugin->getPlayerByRank($rank);
			$line = "§l§b[" . $rank . "] ";
			$line .= isset($banned[$player]) ? "§c[HAVEBAN] " : '';
			$line .= isset($ops[$player]) ? "§a[OP] " : '';
			$line .= "§r§7" . $player . " : " . $this->plugin->myMoney($player);
			$sender->sendMessage($line);
		}
		return true;
	}
}
