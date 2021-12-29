<?php

namespace phuongaz\coin\command;

use phuongaz\coin\Coin;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use jojoe77777\FormAPI\CustomForm;
use pocketmine\Server;
class TopCoinCommand extends CoinCommand
{

    public function __construct(Coin $plugin)
    {
        parent::__construct("toplcoin", $plugin);
    }

    public function _execute(CommandSender $sender, array $args): bool
    {
        if (!$sender instanceof Player) return true;
        $coins = Coin::getAll();
        $maxPage = ceil(count($coins) / 5);
        $page = (isset($args[0]) && is_numeric($args[0]) && (int)$args[0] > 0) ? (int)$args[0] : 1;
        if ($page > $maxPage)
            $page = $maxPage;
        $index1 = $page * 5 - 5;
        $index2 = $page * 5 - 1;
        $count = 0;
        $form = new CustomForm(function(Player $player, ?array $data) use ($maxPage){
            if(is_null($data)) $this->mainForm($player); 
            if(is_numeric($data[1]) and $data[1] <= $maxPage){
                Server::getInstance()->getCommandMap()->dispatch($player, "toplcoin ". $data[1]);
            }else{
                $form = new CustomForm(function(Player $player, ?array $data){});
                $form->setTitle("NOTICE");
                $form->addLabel("§l§cDữ liệu bạn nhập chưa chính xác");
            }
        });

        $form->setTitle("§l§eBẢNG XẾP HẠNG §6LCOIN ");
        $top = "";
       // $rank = ["１","２","３","４","５"];
        $rate = 1;
        $count = 1;
        foreach ($coins as $playerName => $coin) {
            if ($index1 <= $count && $index2 >= $count) {
                //$rate = $count + 1;
               // $rate = $rank[$count];
                $top .= "§l§f{$count}§8. §a" . $playerName . "§f đang sở hữu§a " . $coin . "§a Lcoin. \n";
            }
            ++$rate;
            ++$count;
        }
        $form->addLabel($top);
        $form->addInput("§l§e→ §fTrang (§f".$page."§e/§f".$maxPage.")");
        $form->sendToPlayer($sender);
        return true;
    }
}