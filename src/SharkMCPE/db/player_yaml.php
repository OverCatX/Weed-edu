<?php

namespace SharkMCPE\db;

use pocketmine\Player;
use SharkMCPE\Guncha;

class player_yaml
{

    private $plugin;

    public function __construct(Guncha $plugin)
    {
        $this->plugin = $plugin;
    }

    public function getData(){
        return $this->plugin->getPlayerData();
    }


    public function createData($player){
        $yaml = new yaml($this->plugin);
        $player_data = $this->getData();
        $data = $player_data->getAll();
        $data["Players"][$player->getName()]["time"] = $yaml->getTime();
        $player_data->setAll($data);
        $player_data->save();
    }
    public function getPlayers(): array
    {
        $players = $this->getData();
        $data = $players->getAll();
        return array_keys($data["Players"]);
    }
    public function getCountPlayers(): int
    {
        $players = $this->getData();
        $data = $players->getAll();
        return count($data["Players"]);
    }

    public function setTime($player,$time){
        $player_data = $this->getData();
        $data = $player_data->getAll();
        $data["Players"][$player->getName()]["time"] = $time;
        $player_data->setAll($data);
        $player_data->save();
    }
    public function getTime(Player $player): int{
        $player_data = $this->getData();
        $data = $player_data->getAll();
        return $data["Players"][$player->getName()]["time"];
    }
    public function minusTime(Player $player) {
        $yaml = new yaml($this->plugin);
        if($this->getTime($player) <= 0){
            $this->setTime($player,$yaml->getTime());
        } else {
            $this->setTime($player,$this->getTime($player) - 1);
        }
    }

}