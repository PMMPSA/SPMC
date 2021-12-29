<?php

namespace phuongaz\coin\command;

use phuongaz\coin\Coin;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;
use jojoe77777\FormAPI\CustomForm;


class PayCoinCommand extends CoinCommand
{

    public function __construct(Coin $plugin)
    {
        parent::__construct("paylcoin", $plugin);
        $this->setUsage("/paylcoin [player] [lcoin]");
        $this->setAliases(["lpay", "lcoinpay"]);
    }

    public function _execute(CommandSender $sender, array $args): bool
    {
        if (!$sender instanceof Player) return true;
        if (!isset($args[0]) || !isset($args[1])) {
            $this->onForm($sender);
            //$sender->sendMessage($this->getUsage());
            return true;
        }
        $pay = round((float)array_pop($args), 1);
        $name = implode(" ", $args);
        if (($player = Server::getInstance()->getPlayer($name)) !== null)
            $name = $player->getName();
        if (strtolower($name) === strtolower($sender->getName())) {
            $sender->sendMessage("Không thể tự giao dịch cho chính mình");
            return true;
        }
        $coin = Coin::getCoin($name);
        if ($coin === null) {
            $sender->sendMessage("Không tìm thấy dữ liệu người dùng");
            return true;
        }
        if (!isset($pay) or !is_numeric($pay)) {
            $sender->sendMessage("Thiếu dữ liệu");
            return true;
        }
        if ($pay < 0) {
            $sender->sendMessage("Không hợp lệ.");
            return true;
        }
        $myCoin = Coin::getCoin($sender);
        if ($myCoin < $pay) {
            $sender->sendMessage("§c§lBạn không đủ số Lcoin để chuyển, hãy nạp thêm");
            return true;
        }
            Coin::reduceCoin($sender, $pay);
            Coin::addCoin($name, ($pay * 99 / 100));
        if ($player !== null){
            $player->sendMessage("§l§e{$sender->getName()}§a vừa chuyển cho bạn§e {$pay}§a Lcoin.");
        }
        $sender->sendMessage("§a Đã chuyển $pay Lcoin cho $name");
        return true;
    }

    public function onForm($player){
        if($player instanceof Player){
            $form = new CustomForm(function (Player $player, ?array $data){
                if(is_null($data)) $this->mainForm($player);
                if($data[0] != null){
                    $player = $data[0];
                    if(empty($data[1])) $this->mainForm($player);
                    $money = $data[1];
                    if(!is_numeric($money)) return;
                    $money = intval($money);
                    Coin::getInstance()->getServer()->getCommandMap()->dispatch($player, "paylcoin " . $player . $money);
                }
            });
            $form->setTitle("§l§eＬＣＯＩＮ");
            $form->addInput("§l§a↦ §6Tên người chơi");
            $form->addInput("§l§a↦ §6Số Lcoin cần đưa");
            $form->sendToPlayer($player);
        }
    }
}