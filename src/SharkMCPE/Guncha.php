<?php

namespace SharkMCPE;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use SharkMCPE\cmd\excCommand;
use SharkMCPE\cmd\pos2Command;
use SharkMCPE\cmd\posCommand;
use SharkMCPE\cmd\wdCommand;
use SharkMCPE\task\InArea;

class Guncha extends PluginBase implements Listener
{

    private $data = null;
    private $db = null;
    private $db_player = null;

    public function onEnable()
    {
        $this->cast();
        @mkdir($this->getDataFolder());
        $this->saveDefaultConfig();
        $this->data = new Config($this->getDataFolder() . "operate.yml", Config::YAML, array());
        $this->db = new Config($this->getDataFolder() . "database.yml", Config::YAML, array());
        $this->db_player = new Config($this->getDataFolder() . "players.yml", Config::YAML, array());
        $d = $this->data->getAll();
        $db = $this->db->getAll();
        $db_player = $this->db_player->getAll();
        $this->regcmd();
        $this->regEvent();
        $this->runTask();
        if (!isset($d["Locate"])) {
            $d["Locate"]["Pos1"] = "null";
            $d["Locate"]["Pos2"] = "null";
            $this->data->setAll($d);
            $this->data->save();
        }
        if (!isset($db["Region"])) {
            $db["Region"] = [];
            $this->db->setAll($db);
            $this->db->save();
        }
        if (!isset($db["Settings"])) {
            $db["Settings"]["sneak_warning"] = "โปรด Sneak เพื่อเก็บกัญชา";
            $db["Settings"]["time"] = 10;
            $db["Settings"]["item"]["id"] = 31;
            $db["Settings"]["item"]["meta"] = 2;
            $db["Settings"]["item"]["amount"] = 1;
            $db["Settings"]["item"]["item_name"] = "Weed";
            $this->db->setAll($db);
            $this->db->save();
        }
        if (!isset($db_player["Players"])) {
            $db_player["Players"] = [];
            $this->db_player->setAll($db_player);
            $this->db_player->save();
        }
    }

    private function cast(){
        $this->getLogger()->info("ทำงานแล้ว");
    }

    private function regcmd(){
        $this->getServer()->getCommandMap()->register("pos1", new posCommand($this));
        $this->getServer()->getCommandMap()->register("pos2", new pos2Command($this));
        $this->getServer()->getCommandMap()->register("exc", new excCommand($this));
        $this->getServer()->getCommandMap()->register("wd", new wdCommand($this));
    }

    private function regEvent(){
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this),$this);
    }

    private function runTask(){
        $this->getScheduler()->scheduleRepeatingTask(new InArea($this), 20);
    }

    public function getData(){
        return $this->data;
    }
    public function getDatabase(){
        return $this->db;
    }
    public function getPlayerData(){
        return $this->db_player;
    }


}