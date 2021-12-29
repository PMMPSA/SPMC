<?php

namespace phuongaz\coin\command;

use lang\Lang;
use phuongaz\coin\Coin;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;
use jojoe77777\FormAPI\CustomForm;
class SeeCoinCommand extends CoinCommand
{

    public function __construct(Coin $plugin)
    {
        parent::__construct("seelcoin", $plugin);
        $this->setAliases(["seecoin", "slcoin"]);
    }

    public function _execute(CommandSender $sender, array $args): bool
    {
        $form = new CustomForm(function(Player $player, ?array $data){});
        $form->setTitle("§l§eＬＣＯＩＮ");
        if (!$sender instanceof Player) {
            $form->addLabel("§cNhập sai tên người chơi");
            $form->sendToPlayer($sender);
            return true;
        }
        if (!isset($args[0]))
            $name = $sender->getName();
        else {
            $name = array_shift($args);
            if (($player = Server::getInstance()->getPlayer($name)) !== null)
                $name = $player->getName();
        }
        $coin = Coin::getCoin($name);
        if ($coin === null) {
            $form->addLabel("Không tìm thấy dữ liệu");
            $form->sendToPlayer($sender);
            return true;
        }
        $form->addLabel("§l§aSố Lcoin người chơi§e ".$name."§a đang sở hữu:§a ". $coin);
        $form->addLabel("§l§aThuộc Top:§e ". Coin::getRank($sender));
        $form->sendToPlayer($sender);
        return true;
    }

    public function onForm($player){
        if($player instanceof Player){
            $form = new CustomForm(function (Player $player, ?array $data){
                if(is_null($data)) $this->mainForm($player);
                $result = $data[0];
                if($result != null){
                    $this->playerNamer = $data[0];
                    Server::getInstance()->getCommandMap()->dispatch($player, "seelcoin " . $this->playerNamer);
                }
            });
            $form->setTitle("§l§eＬＣＯＩＮ");
            $form->addInput("§l§a↦ §6Tên người chơi");
            $form->sendToPlayer($player);
        }
    }
}