<?php

namespace phuongaz\coin\command;

use phuongaz\coin\Coin;
use pocketmine\command\CommandSender;
use pocketmine\permission\Permission;
use pocketmine\Player;
use pocketmine\Server;

class SetCoinCommand extends CoinCommand
{

    public function __construct(Coin $plugin)
    {
        parent::__construct("setlcoin", $plugin);
        $this->setAliases(["setcoin", "setcoin"]);
        $this->setUsage("/setlcoin [player] [coin]");
        $this->setDescription(".");
        $this->setPermission(Permission::DEFAULT_OP);
    }

    public function _execute(CommandSender $sender, array $args): bool
    {
        if (!$sender instanceof Player) return true;
        if (!$sender->hasPermission($this->getPermission())) return true;

        if (!isset($args[0])) {
            $sender->sendMessage( $this->getUsage());
            return true;
        }
        $name = array_shift($args);
        if (($player = Server::getInstance()->getPlayer($name)) !== null)
            $name = $player->getName();
        $coin = Coin::getCoin($name);
        if ($coin === null) {
            $sender->sendMessage( $name . " khong co du lieu.");
            return true;
        }
        $coin = array_shift($args);
        if (!isset($coin) or !is_numeric($coin)) {
            $sender->sendMessage( " nhap sai coin me roi.");
            return true;
        }
        Coin::setCoin($name, (float)$coin);
        $sender->sendMessage($name . " set " . $coin . " Ok.");
        return true;
    }

}