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
use onebone\economyapi\event\money\PayMoneyEvent;

class PayCommand extends Command{
	private $plugin;

	public function __construct(EconomyAPI $plugin){
		parent::__construct("pay", "chuyen tien cho nguoi choi khac.", "/pay [player] [xu]", ["chuyenxu", "pay"]);
		$this->setPermission("economyapi.command.pay");

		$this->plugin = $plugin;
	}

	public function execute(CommandSender $sender, string $label, array $params) : bool{
/*		if(!$sender->hasPermission($this->getPermission())){
			$sender->sendMessage(EconomyAPI::$prefix . "No.");
			return true;
		}*/

		if(!$sender instanceof Player){
			$sender->sendMessage(EconomyAPI::$prefix . "인게임에서만 사용 가능합니다.");
			return true;
		}

		$player = array_shift($params);
		$amount = array_shift($params);

		if(!is_numeric($amount)){
			$sender->sendMessage(EconomyAPI::$prefix . " Xu phải là số : " . $this->getUsage());
			return true;
		}

		if(($p = $this->plugin->getServer()->getPlayer($player)) instanceof Player){
			$player = $p->getName();
		}

		if(!$p instanceof Player and $this->plugin->getConfig()->get("allow-pay-offline", true) === false){
			$sender->sendMessage(EconomyAPI::$prefix ." Không tìm thấy người chơi §e". $player . ".");
			return true;
		}

		if(!$this->plugin->accountExists($player)){
			$sender->sendMessage(EconomyAPI::$prefix ."§l§f người chơi §c" .$player . " §fkhông tồn tại.");
			return true;
		}

		$this->plugin->getServer()->getPluginManager()->callEvent($ev = new PayMoneyEvent($this->plugin, $sender->getName(), $player, $amount));

		$result = EconomyAPI::RET_CANCELLED;
		if(!$ev->isCancelled()){
			$result = $this->plugin->reduceMoney($sender, $amount);
		}

		if($result === EconomyAPI::RET_SUCCESS){
			$this->plugin->addMoney($player, $amount, true);

			$sender->sendMessage(EconomyAPI::$prefix ." Đã chuyển thành công cho người chơi §e". $player . "§f với số xu§e " . $amount);
			if($p instanceof Player){
				$p->sendMessage(EconomyAPI::$prefix ." Người chơi §e" .$sender->getName() . " §fđã chuyển cho bạn §e" . $amount . " §fxu.");
			}
		}else{
			$sender->sendMessage(EconomyAPI::$prefix . "Chuyển thất bại.");
		}
		return true;
	}
}
