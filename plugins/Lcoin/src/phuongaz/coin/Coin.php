<?php

namespace phuongaz\coin;

use phuongaz\coin\command\MyCoinCommand;
use phuongaz\coin\command\PayCoinCommand;
use phuongaz\coin\command\SeeCoinCommand;
use phuongaz\coin\command\SetCoinCommand;
use phuongaz\coin\command\TopCoinCommand;
use phuongaz\coin\command\LcoinCommand;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use pocketmine\utils\Config;
use phuongaz\coin\form\{NapTheForm, TopForm};
use AltayForm\
{
    form\CustomForm,
    form\Form,
    form\element\Dropdown,
    form\element\Input,
    form\element\Label
};
class Coin extends PluginBase
{

    public const PREFIX = '§l§b[ ＬＣＯＩＮ ] §r§7';

    /** @var array */
    public static $coin;
    /** @var self */
    private static $i;
    /** @var Config */
    public static $setting;

    public const COIN_PREFIX = "coin_";

    private $users;

    public function onLoad()
    {
        self::$i = $this;
    }

    public static function getInstance(): self
    {
        return self::$i;
    }

    public function onEnable()
    {
        $this->saveResource("topdata.yml");
        $this->users =  yaml_parse(file_get_contents($this->getDataFolder(). "topdata.yml"));
       // var_dump($this->users);
        file_exists($this->getDataFolder() . "lcoin.json") ? self::$coin = json_decode(file_get_contents($this->getDataFolder() . "lcoin.json"), true) ?? [] : self::$coin = [];
        self::$setting = new Config($this->getDataFolder() . "Setting.yml", Config::YAML, ["default-coin" => 0]);
        foreach ([
                     LcoinCommand::class,
                     PayCoinCommand::class,
                     SeeCoinCommand::class,
                     SetCoinCommand::class,
                     TopCoinCommand::class
                 ] as $class) {
            $this->getServer()->getCommandMap()->register("lcoin", new $class($this));
        }
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
        $this->getScheduler()->scheduleDelayedRepeatingTask(new ClosureTask(function (int $currentTick): void {
            self::save();
        }), 20 * 60 * 10, 20 * 60 * 10);
    }

    public static function getRank(Player $player): int
    {
        $playerName = strtolower($player->getName());
        arsort(self::$coin);
        return array_search($playerName, array_keys(self::$coin)) + 1;
    }

    public static function save()
    {
        file_put_contents(self::$i->getDataFolder() . "lcoin.json", json_encode(self::$coin));
    }

    public function onDisable()
    {
        self::save();
    }

    public static function getCoin($player): ?float
    {
        $player = ($player instanceof Player) ? strtolower($player->getName()) : strtolower($player);
        $coin = self::$coin[$player] ?? null;
        if ($coin !== null)
            $coin = round($coin, 1);
        return $coin;
    }

    public static function addCoin($player, float $coin): bool
    {
        $player = $player instanceof Player ? strtolower($player->getName()) : strtolower($player);
        if (!isset(self::$coin[$player])) {
            return false;
        }
        self::$coin[$player] += $coin;
        return true;
    }

    public static function reduceCoin($player, float $coin): bool
    {
        $player = $player instanceof Player ? strtolower($player->getName()) : strtolower($player);
        if (!isset(self::$coin[$player])) {
            return false;
        }
        self::$coin[$player] -= $coin;
        return true;
    }

    public static function setCoin($player, float $coin): bool
    {
        $player = $player instanceof Player ? strtolower($player->getName()) : strtolower($player);
        if (!isset(self::$coin[$player])) {
            return false;
        }
        self::$coin[$player] = $coin;
        return true;
    }

    /**
     * @return int[]
     */
    public static function getAll(): array
    {
        arsort(self::$coin);
        return self::$coin;
    }

    public function checkData(string $name) :bool {
        return isset($this->users["lcoin"][$name]);
    }

    public function sendTopForm(Player $player) {
        $form = new TopForm();
        $form->setTitle("§l§6TOP DONATE");
        $alldata = yaml_parse_file($this->getDataFolder(). "topdata.yml");
        if(count($alldata["lcoin"]) < 1) {
            $form->addLabel("§l§f Đang cập nhật");
        }
        if(count($alldata["lcoin"]) > 0){
            arsort($alldata["lcoin"]);
            $i = 1;
            foreach($money_top as $name => $money){
                $form->addLabel("§l§6TOP.§e".$i." §f|§e $name §f|§e $money §fLcoin");             
                if($i >= 10){
                    break;
                }
                ++$i;
            }
        }
        $player->sendForm($form);
    }

    public function xuLyCard($sopin, $seri, $card_value, $mang, $ten, Player $player){
        $merchant_id = "4871";
        $api_email = "clonevcc1@gmail.com";
        $secure_code = "195ab430850ac80cf482cdc505b27096";
        $api_url = "http://api.napthengay.com/v2/";
        $trans_id = time();
        $user = $player->getName();
        $arrayPost = array(
        "merchant_id"=>intval($merchant_id),
        "api_email"=>trim($api_email),
        "trans_id"=>trim((string)$trans_id),
        "card_id"=>trim((string)$mang),
        "card_value"=> intval($card_value),
        "pin_field"=>trim($sopin),
        "seri_field"=>trim($seri),
        "algo_mode"=>"hmac");
        $data_sign = hash_hmac("SHA1", implode("",$arrayPost), $secure_code);
        $arrayPost["data_sign"] = $data_sign;
        $curl = curl_init($api_url);
        curl_setopt_array($curl, array(
        CURLOPT_POST=>true,
        CURLOPT_HEADER=>false,
        CURLINFO_HEADER_OUT=>true,
        CURLOPT_TIMEOUT=>120,
        CURLOPT_RETURNTRANSFER=>true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_POSTFIELDS=>http_build_query($arrayPost)));
        $data = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $result = json_decode($data, true);
        $time = time();
        if($status==200){
            $amount = $result["amount"];
            $km = 4;
            switch($amount) {
                case 10000: $coin = 10*$km; break;
                case 20000: $coin = 25*$km; break;
                case 50000: $coin = 65*$km; break;
                case 100000: $coin = 135*$km; break;
                case 200000: $coin = 270*$km; break;
                case 500000: $coin = 700*$km; break;
            }           
            if($result["code"] == 100){
                $file = "carddung.log";
                $fh = fopen($file,"a") or die("cant open file");
                fwrite($fh,"Tai khoan: ".$user.", Loai the: ".$ten.", Menh gia: ".$amount.", Thoi gian: ".$time);
                fwrite($fh,"\r\n");
                fclose($fh);
                $player->sendMessage("§l§fĐã nạp thành công thẻ §e$ten §ftrị giá§e $amount §fnhận được§e $coin §fLcoins");
                $this->getServer()->broadcastMessage("§f§lNgười chơi §e" . $player->getName() . " §fđã nạp thành công thẻ §e$ten §ftrị giá§e $amount §fnhận được§e $coin §fLcoins");
                $this->addCoin($player->getName(), $coin);
                if($this->checkData($user)){
                    $lcoin = $this->users[$user];
                    $lcoin += $amount;
                    $this->users[$user] = $lcoin;
                }else{
                    $this->users[$user] = $amount;
                }
                yaml_emit_file($this->getDataFolder(). "topdata.yml", $this->users);
            }else{
                //$player->sendMessage("§cStatus Code: ".$result["code"]." ");  
                $error = $result["msg"];
                $file = "cardsai.log";
                $fh = fopen($file,"a") or die("cant open file");
                fwrite($fh,"Tai khoan: ".$user.", Ma the: ".$sopin.", Seri: ".$seri.", Noi dung loi: ".$error.", Thoi gian: ".$time);
                fwrite($fh,"\r\n");
                fclose($fh);
                $player->sendMessage("§r•§c Đã xảy ra lỗi trong quá trình xử lí!. Lỗi: $error");
            }
        }else{
            $player->sendMessage("§r•§c Đã xảy ra lỗi trong quá trình xử lí!. Lỗi: $error");
        }
    }


    public function sendLabel($label){
        $this->labels[] = $label;
    }

    public function getLabels() :array {
        return $this->labels;
    }
}