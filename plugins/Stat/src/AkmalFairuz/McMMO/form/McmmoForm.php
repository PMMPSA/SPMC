<?php

/*
 *
 *              _                             _        ______             _
 *     /\      | |                           | |      |  ____|           (_)
 *    /  \     | | __   _ __ ___      __ _   | |      | |__       __ _    _    _ __    _   _    ____
 *   / /\ \    | |/ /  | '_ ` _ \    / _` |  | |      |  __|     / _` |  | |  | '__|  | | | |  |_  /
 *  / ____ \   |   <   | | | | | |  | (_| |  | |      | |       | (_| |  | |  | |     | |_| |   / /
 * /_/    \_\  |_|\_\  |_| |_| |_|   \__,_|  |_|      |_|        \__,_|  |_|  |_|      \__,_|  /___|
 *
 * Discord: akmal#7191
 * GitHub: https://github.com/AkmalFairuz
 *
 */

namespace AkmalFairuz\McMMO\form;

use AkmalFairuz\McMMO\formapi\FormAPI;
use AkmalFairuz\McMMO\Main;
use pocketmine\Player;

class McmmoForm
{
    /** @var Main */
    private $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    public function init(Player $player) {
        $form = (new FormAPI())->createSimpleForm(function (Player $player, $data) {
            if($data === null) {
                return;
            }
            switch($data) {
                case 0:
                    $this->stats($player);
                    return;
                case 1:
                    $this->leaderboard($player);
                    return;
                case 2:
                    $form = new ChangeRewards($player);
                    $form->init();
                    break;
            }
        });
        $form->setTitle("§l§eĐIỂM TÍCH LŨY");
        $form->addButton("§l§f•§0 Thông tin của bạn §f•");
        $form->addButton("§l§f•§0 Điếm §f•");
        $form->addButton("§l§f•§0 Đỏi quà §f•");
        $form->sendToPlayer($player);
    }

    public function stats(Player $player) {
        $form = (new FormAPI())->createSimpleForm(function (Player $player, $data) {
            if($data !== null) {
                $this->init($player);
            }
        });
        $form->setTitle("§l§6Thông tin Điểm");
        $content = [
            "§l§c→§f Lumberjack: ",
            "  §c+§f Kinh Nghiệm:§e ".$this->plugin->getXp(Main::LUMBERJACK, $player),
            "  §c+§f Điểm:§e ".$this->plugin->getLevel(Main::LUMBERJACK, $player),
            "§l§c→§f Farmer: ",
            "  §c+§f Kinh Nghiệm:§e ".$this->plugin->getXp(Main::FARMER, $player),
            "  §c+§f Điểm:§e ".$this->plugin->getLevel(Main::FARMER, $player),
            "§l§c→§f Miner: ",
            "  §c+§f Kinh Nghiệm:§e ".$this->plugin->getXp(Main::MINER, $player),
            "  §c+§f Điểm:§e ".$this->plugin->getLevel(Main::MINER, $player),
            "§l§c→§f Killer: ",
            "  §c+§f Kinh Nghiệm:§e ".$this->plugin->getXp(Main::KILLER, $player),
            "  §c+§f Điểm:§e ".$this->plugin->getLevel(Main::KILLER, $player),
            "§l§c→§f Combat: ",
            "  §c+§f Kinh Nghiệm:§e ".$this->plugin->getXp(Main::COMBAT, $player),
            "  §c+§f Điểm:§e ".$this->plugin->getLevel(Main::COMBAT, $player),
            "§l§c→§f Builder: ",
            "  §c+§f Kinh Nghiệm:§e ".$this->plugin->getXp(Main::BUILDER, $player),
            "  §c+§f Điểm:§e ".$this->plugin->getLevel(Main::BUILDER, $player),
            "§l§c→§f Archer: ",
            "  §c+§f Kinh Nghiệm:§e ".$this->plugin->getXp(Main::ARCHER, $player),
            "  §c+§f Điểm:§e ".$this->plugin->getLevel(Main::ARCHER, $player),
            "§l§c→§f Lawn Mower: ",
            "  §c+§f Kinh Nghiệm:§e ".$this->plugin->getXp(Main::LAWN_MOWER, $player),
            "  §c+§f Điểm:§e ".$this->plugin->getLevel(Main::LAWN_MOWER, $player)
        ];
        $string = implode("\n", $content);
        $string = $this->plugin->translate($string);
        $form->setContent($string);
        $form->addButton("§l§f•§0 Trở về §f•");
        $form->sendToPlayer($player);
    }

    public function leaderboard(Player $player) {
        $a = ["Lumberjack", "Farmer", "Miner", "Killer", "Combat", "Builder", "Archer", "Lawn Mower"];
        $form = (new FormAPI())->createSimpleForm(function (Player $player, $data) use ($a) {
            if($data === null) {
                $this->init($player);
                return;
            }
            if($data === count($a)) {
                $this->init($player);
                return;
            }
            $this->leaderboards($player, $data);
        });
        $form->setTitle("§l§6Bảng xếp hạng");
        $form->setContent("");
        foreach($a as $as) {
            $form->addButton("§l§f•§0 ". $this->plugin->translate($as) ." §f•");
        }
        $form->addButton("§l§f•§0 Trở về §f•");
        $form->sendToPlayer($player);
    }

    public function leaderboards(Player $player, int $type) {
        $form = (new FormAPI())->createSimpleForm(function (Player $player, $data) {
            $this->leaderboard($player);
        });
        $a = ["Lumberjack", "Farmer", "Miner", "Killer", "Combat", "Builder", "Archer", "Lawn Mower"];
        $form->setTitle("§l§6Bảng xếp hạng§e ".$a[$type]);
        $content = "";
        $a = $this->plugin->getAll($type);
        arsort($a);
        $i = 1;
        foreach($a as $key => $as) {
            if($i == 20) break;
            $content .= "§lTOP§f ".$i.". §e".$key . " §f|§a ".$as." §eđiểm\n";
            $i++;
        }
        $string = $this->plugin->translate($content);
        $form->setContent("§l§aTên người chơi §d|§a Điểm\n\n".$string);
        $form->addButton("§l§f•§0 Trở về §f•");
        $form->sendToPlayer($player);
    }
}
