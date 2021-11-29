<?php

namespace SharkMCPE\form;

use pocketmine\level\Location;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;
use SharkMCPE\db\player_yaml;
use SharkMCPE\db\yaml;
use SharkMCPE\Guncha;
use SharkMCPE\libs\xenialdan\customui\elements\Button;
use SharkMCPE\libs\xenialdan\customui\elements\Input;
use SharkMCPE\libs\xenialdan\customui\elements\Label;
use SharkMCPE\libs\xenialdan\customui\windows\CustomForm;
use SharkMCPE\libs\xenialdan\customui\windows\SimpleForm;

class Form
{
    private $plugin;

    public function __construct(Guncha $plugin){
        $this->plugin = $plugin;
    }
    public function getPlugin(): Guncha{
        return $this->plugin;
    }

    public function Form(Player $player, string $content = ""): void{
        $array = [];
        $yaml = new yaml($this->getPlugin());
        if($yaml->getCountRegion() !== 0){
            foreach($yaml->getRegion() as $regions){
                $array[] = $regions;
            }
        }
        $form = new SimpleForm("Region", $content);
        $form->addButton(new Button("Create Region\nสร้างพื้นที่"));
        $form->addButton(new Button("Setting\nตั้งค่าข้อความ,เวลา.อื่นๆ"));
        for($i = 0; $i < count($array); $i++){
            $form->addButton(new Button($array[$i]));
        }
        $form->setCallable(function (Player $player, $data) use ($array){
            if(!($data === null)){
                switch($data){
                    case ("Create Region\nสร้างพื้นที่"):
                        $this->CreateRegion($player);
                        break;
                    case ("Setting\nตั้งค่าข้อความ,เวลา.อื่นๆ"):
                        $this->Setting($player);
                        break;
                    default:
                        $this->Region($player,$data);
                        break;
                }
            }
        });
        $form->setCallableClose(function (Player $player){
        });
        $player->sendForm($form);
    }

    public function CreateRegion(Player $player,$text=""): void{
        $form = new CustomForm("CreateRegion");
        $form->addElement(new Label($text));
        $form->addElement(new Input("ชื่อพื้นที่", "region1")); //1
        $form->setCallable(function (Player $player, $data) {
            if ($data == null) {
                return;
            }
            $name = explode(" ", $data[1]);
            if ($name[0] == null) {
                $text = "§f[§bR§ce§ag§bi§eo§6n§f] §cโปรดใส่ชื่อ";
                $this->CreateRegion($player, $text);
                return;
            }
            $yaml = new yaml($this->getPlugin());
            $name = $name[0];
            if ($yaml->issetRegion($name)) {
                $text = "§f[§bR§ce§ag§bi§eo§6n§f] §cคุณมีชื่อพื้นที่นี้อยู่แล้ว";
                $this->CreateRegion($player, $text);
                return;
            }
            $yaml->createRegion($name);
            $player->sendMessage("§f[§bR§ce§ag§bi§eo§6n§f]] §aคุณได้สร้างพื้นที่ " . $name . " §aเรียบร้อยแล้ว");
            $player->sendMessage("§f- §l§cโปรด /p1 เพื่อบันทึกจุดที่ 1");
            $player->sendMessage("§f- §l§cตามด้วย /p2 เพื่อบันทึกจุดที่ 2");
            $player->sendMessage("§f- §l§cและ /exc (ชื่อพื้นที่) เพื่อบันทึกบันทึกพื้นที่");
        });
        $form->setCallableClose(function (Player $player) {
        });
        $player->sendForm($form);
    }
    public function Setting(Player $player): void{
        $form = new CustomForm("ตั้งค่าข้อความ,เวลา");
        $form->addElement(new Label(""));
        $yaml = new yaml($this->getPlugin());
        $form->addElement(new Input("เตือนเมื่อไม่กดย่อตอนเก็บกัญชา", (string)$yaml->getSetting("sneak_warning")));
        $form->addElement(new Input("เวลาเมื่อเก็บกัญชา[กรอกจำนวนเต็ม]", (string)$yaml->getSetting("time")));
        $form->addElement(new Input("ชื่อไอเทม [กัญชา]", (string)$yaml->getSettingTwofac("item","item_name")));
        $form->addElement(new Input("id [กัญชา]", (string)$yaml->getSettingTwofac("item","id")));
        $form->addElement(new Input("meta [กัญชา]", (string)$yaml->getSettingTwofac("item","meta")));
        $form->addElement(new Input("amount [กัญชา]", (string)$yaml->getSettingTwofac("item","amount")));
        $form->setCallable(function ($player, $data){
//            if($data == null){
//                return;
//            }
            $yaml = new yaml($this->getPlugin());
            $player_db = new player_yaml($this->getPlugin());
            $warning_sneak = $data[1];
            $time = $data[2];
            $item_name = $data[3];
            $id = $data[4];
            $meta = $data[5];
            $amount = $data[6];
            if ($warning_sneak == null) {
                $warning_sneak = $yaml->getSetting("sneak_warning");
            } else {
                $warning_sneak = (string)$data[1];
            }
            if ($time == null) {
                $time = $yaml->getSetting("time");
            } else {
                $time = (int)$data[2];
            }
            if (!is_numeric($time) || $time < 0) {
                $text = "§f[§bR§ce§ag§bi§eo§6n§f] §cโปรดกรอกจำนวนเต็มบวก §f(§cเวลาเมื่อเก็บกัญชา§f)";
                $this->Setting($player, $text);
                return;
            }
            if ($item_name == null) {
                $item_name = (string)$yaml->getSettingTwofac("item","item_name");
            } else {
                $item_name = (string)$data[3];
            }
            if (!is_numeric($id) || $id == null) {
                $id = $yaml->getSettingTwofac("item","id");
            } else {
                $id = (int) $data[4];
            }
            if (!is_numeric($meta) || $meta == null) {
                $meta = (int) $yaml->getSettingTwofac("item","meta");
            } else {
                $meta = (int) $data[5];
            }
            if (!is_numeric($amount) || $amount == null) {
                $amount = (int) $yaml->getSettingTwofac("item","amount");
            } else {
                $amount = (int) $data[6];
            }
//            echo "warning_sneak: ".$warning_sneak." time: ".$time." id: ".$id.
//                " meta: ".$meta." amount: ".$amount." item_name: ".$item_name.""; ตรงนี้ลบได้นะเอามาเช็ค recept เฉยๆ
            $yaml->editSetting($warning_sneak,$time,$id,$meta,$amount,$item_name);
            foreach ($this->getPlugin()->getServer()->getOnlinePlayers() as $players){
                $player_db->setTime($players,$time);
            }
            $player->sendMessage("§f[§bR§ce§ag§bi§eo§6n§f] §aคุณแก้ไขการตั้งค่าเรียบร้อยแล้ว");
        });
        $form->setCallableClose(function (Player $player){
        });
        $player->sendForm($form);
    }
    public function Region(Player $player, string $region): void{
        $form = new SimpleForm("Region ".$region, "");
        $form->addButton(new Button("Teleport to Region\nวาร์ปไปที่พื้นที่"));
        $form->addButton(new Button("Delete Region\nลบพื้นที่"));
        $form->setCallable(function (Player $player, $data) use ($region){
            if(!($data === null)){
                switch($data){
                    case ("Teleport to Region\nวาร์ปไปที่พื้นที่"):
                        $yaml = new yaml($this->getPlugin());
                        $x = $yaml->getPos($region, "Pos1","x");
                        $y = $yaml->getPos($region, "Pos1","y");
                        $z = $yaml->getPos($region, "Pos1","z");
                        $world = $yaml->getWorld($region);
                        $player->teleport(new Position($x,$y,$z, $this->getPlugin()->getServer()->getLevelByName($world)));
                        $player->sendMessage("§f[§bR§ce§ag§bi§eo§6n§f] §aคุณได้ teleport ไปยังพื้นที่§6 ".$region."§a สำเร็จแล้ว");
                        break;
                    case ("Delete Region\nลบพื้นที่"):
                        $yaml = new yaml($this->getPlugin());
                        $yaml->deleteRegion($region);
                        $player->sendMessage("§f[§bR§ce§ag§bi§eo§6n§f] §aคุณได้ลบพื้นที่§6 ".$region."§a สำเร็จแล้ว");
                        break;
                }
            }
        });
        $form->setCallableClose(function (Player $player){
        });
        $player->sendForm($form);
    }

}