<?php

namespace SharkMCPE\db;

use SharkMCPE\Guncha;

class yaml
{

    private $plugin;

    public function __construct(Guncha $plugin)
    {
        $this->plugin = $plugin;
    }

    public function getData(){
        return $this->plugin->getData();
    }

    public function getDatabase(){
        return $this->plugin->getDatabase();
    }

    public function setPos1($x,$y,$z){
        $region = $this->getData();
        $data = $region->getAll();
        $data["Locate"]["Pos1"] = $x."".".".$y.".".$z;
        $region->setAll($data);
        $region->save();
    }

    public function setPos2($x,$y,$z){
        $region = $this->getData();
        $data = $region->getAll();
        $data["Locate"]["Pos2"] = $x."".".".$y.".".$z;
        $region->setAll($data);
        $region->save();
    }

    public function getPos1(): string{
        $region = $this->getData();
        $data = $region->getAll();
        return $data["Locate"]["Pos1"];
    }

    public function getPos2(): string{
        $region = $this->getData();
        $data = $region->getAll();
        return $data["Locate"]["Pos2"];
    }

    public function executeData($name,$pos1,$pos2,$world){
        $region = $this->getDatabase();
        $data = $region->getAll();
        //Pos1
        $pos = explode(".",$pos1); //pos[0] = x, pos[1] = y, pos[2] = z;
        $data["Region"][$name]["Pos1"]["x"] = $pos[0];
        $data["Region"][$name]["Pos1"]["y"] = $pos[1];
        $data["Region"][$name]["Pos1"]["z"] = $pos[2];
        //Pos2
        $pos = explode(".",$pos2); //pos[0] = x, pos[1] = y, pos[2] = z;
        $data["Region"][$name]["Pos2"]["x"] = $pos[0];
        $data["Region"][$name]["Pos2"]["y"] = $pos[1];
        $data["Region"][$name]["Pos2"]["z"] = $pos[2];
        $data["Region"][$name]["world"] = $world;
        $region->setAll($data);
        $region->save();
    }

    public function createRegion($name){
        $region = $this->getDatabase();
        $data = $region->getAll();
        $data["Region"][$name]["Pos1"]["x"] = 9999999999;
        $data["Region"][$name]["Pos1"]["y"] = 9999999999;
        $data["Region"][$name]["Pos1"]["z"] = 9999999999;
        $data["Region"][$name]["Pos2"]["x"] = 9999999999;
        $data["Region"][$name]["Pos2"]["y"] = 9999999999;
        $data["Region"][$name]["Pos2"]["z"] = 9999999999;
        $data["Region"][$name]["world"] = "world";
        $region->setAll($data);
        $region->save();
    }

    public function getPos($name, $type,$typePos): int{
        $region = $this->getDatabase();
        $data = $region->getAll();
        return $data["Region"][$name][$type][$typePos];
    }
    public function getWorld($name): string{
        $region = $this->getDatabase();
        $data = $region->getAll();
        return $data["Region"][$name]["world"];
    }

    public function resetPos(){
        $region = $this->getData();
        $data = $region->getAll();
        $data["Locate"]["Pos1"] = "null";
        $data["Locate"]["Pos2"] = "null";
        $region->setAll($data);
        $region->save();
    }

    public function getTime(): int{
        $region = $this->getDatabase();
        $data = $region->getAll();
        return $data["Settings"]["time"];
    }
    public function getSetting($type){
        $region = $this->getDatabase();
        $data = $region->getAll();
        return $data["Settings"][$type];
    }
    public function getMsg(): string{
        $region = $this->getDatabase();
        $data = $region->getAll();
        return $data["Settings"]["sneak_warning"];
    }
    public function getSettingTwofac($type,$name){
        $region = $this->getDatabase();
        $data = $region->getAll();
        return $data["Settings"][$type][$name];
    }

    public function getCountRegion(): int
    {
        $regions = $this->getDatabase();
        $data = $regions->getAll();
        return count($data["Region"]);
    }
    public function getRegion(): array
    {
        $regions = $this->getDatabase();
        $data = $regions->getAll();
        return array_keys($data["Region"]);
    }
    public function issetRegion(string $name): bool
    {
        $regions = $this->getDatabase();
        $data = $regions->getAll();
        return isset($data["Region"][$name]);
    }
    public function editSetting($msg_sneak,$time,$id,$meta,$amount,$item_name){
        $player_yaml = new player_yaml($this->plugin);
        $region = $this->getDatabase();
        $data = $region->getAll();
        $data["Settings"]["sneak_warning"] = $msg_sneak;
        $data["Settings"]["time"] = $time;
        $data["Settings"]["item"]["id"] = $id;
        $data["Settings"]["item"]["meta"] = $meta;
        $data["Settings"]["item"]["amount"] = $amount;
        $data["Settings"]["item"]["item_name"] = $item_name;
        $region->setAll($data);
        $region->save();
//        if ($player_yaml->getCountPlayers() !== 0) { //Count Players (int)
//            foreach ($player_yaml->getPlayers() as $players) {
//                $array[] = $players;
//            }
//        }
//        foreach ($array as $players) { //Loop Count_player time++
//            $player_yaml->setTime($players,$time);
//        }
    }
    public function deleteRegion(string $name): void
    {
        $regions = $this->getDatabase();
        $data = $regions->getAll();
        unset($data["Region"][$name]);
        $regions->setAll($data);
        $regions->save();
    }

}