<?php

namespace phuongaz\LOCMCore\Task;

use pocketmine\Server;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat as C;
use phuongaz\LOCMCore\Loader;

class BroadcastTask extends Task{

    public function onRun(int $currentTick) : void{
        /** @var array $input */
        $input = [
            "§l§e/is tp§f để về đảo nhanh chống mà không cần vào cổng!",
            "§l§aTích điểm đỏi quà, xem thông tin tại §f(§d/point§f)",
            "§l§fĐến ngay pvp hoặc dungeon nhanh chống bằng lệnh §l§e/dungeon, /pvp",
            "§l§fSử dụng lệnh §e/mission§f để xem chỉ tiêu hôm nay!",
            "§l§f Đống góp ủng hộ máy chủ bằng lệnh §e/lcoin",
            "§l§f Những vấn đề §eHack, Cheat, Bug, Lỗi,...§f báo ngay cho admin để có hướng giải quyết",
            "§l§eDụng cụ hoặc giáp của bạn đang hư?, sửa ngay bằng lệnh §e/fix",
            "§l§eX4§f giá trị nạp thẻ đến hết §e18/2§f (Mùng 7 tết)"
        ];
        $details = array_rand($input);
        Loader::broadcast(C::GRAY . $input[$details]);
    }
}
