<?php

namespace phuongaz\coin\command;

use lang\Lang;
use phuongaz\coin\Coin;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;
use jojoe77777\FormAPI\SimpleForm;
use phuongaz\coin\form\NapTheForm2;
use phuongaz\coin\form\Status;
use phuongaz\coin\form\Price;
use phuongaz\coin\form\Momo;
class LcoinCommand extends CoinCommand
{

    public function __construct(Coin $plugin)
    {
        parent::__construct("lcoin", $plugin);
       // $this->setAliases(["seecoin", "slcoin"]);
    }

    public function _execute(CommandSender $sender, array $args): bool
    {
        if($sender instanceof Player){
                $form = new SimpleForm(function (Player $player, ?int $data){
                if(isset($data)){
                    switch($data){
                        case 0:
                        Coin::getInstance()->getServer()->getCommandMap()->dispatch($player, "paylcoin");
                        break;
                        case 1:
                        Coin::getInstance()->getServer()->getCommandMap()->dispatch($player, "toplcoin");
                        break;
                        case 2:
                        Coin::getInstance()->getServer()->getCommandMap()->dispatch($player, "seelcoin");
                        break;
                        case 3:
                        $simple = new SimpleForm(function(Player $player, ?int $data){
                            if(is_null($data)){
                                Coin::getInstance()->getServer()->getCommandMap()->dispatch($player, "lcoin");
                                return;
                            }
                            if($data == 0){
                                $form = new Status();
                                $form->__init();
                                $form->setTitle("§l§6LOCM DONATE");
                                $player->sendForm($form);                                
                            }
                            if($data == 1){
                                $form = new Momo();
                                $form->__init();
                                $form->setTitle("§l§6LOCM DONATE");
                                $player->sendForm($form);
                            }
                            if($data == 2) Coin::getInstance()->sendTopForm($player);
                            if($data == 3){
                                $form = new Price();
                                $form->setTitle("LOCM DONATE");
                                $form->__init();
                                $player->sendForm($form);
                            }
                        });
                        $simple->setTitle("§l§6LOCM DONATE");
                        $simple->addButton("§l§f•§0 Thẻ Cào §f•");
                        $simple->addButton("§l§f•§0 Momo §f•");
                        $simple->addButton("§l§f•§0 BXH Donate §f");
                        $simple->addButton("§l§f•§0 Bản Gía Nạp Thẻ §f");
                        $simple->sendToPlayer($player);
                        break;
                        case 4:
                        Coin::getInstance()->getServer()->getCommandMap()->dispatch($player, "lcoinshop");
                        break;
                    }
                }
            });
            $p = Coin::getInstance()->getCoin($sender);
            $form->setTitle("§l§eＬＣＯＩＮ");
            $form->setContent("§l§e↦ §fYour Lcoin:§e ".$p." $");
            $form->addButton("§l§f•§0 Chuyển tiền §f•");//0
            $form->addButton("§l§f•§0 BXH Lcoin §f•");//1
            $form->addButton("§l§f•§0 Xem Lcoin người khác §f•"); //2
            $form->addButton("§l§f•§0 Nạp Lcoin §f•"); //3
            $form->addButton("§l§f•§0 Cửa Hàng Lcoin §f•"); //4
            $form->sendToPlayer($sender);
        }
        return true;
    }
        
}