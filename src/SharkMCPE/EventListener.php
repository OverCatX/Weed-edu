<?php

namespace SharkMCPE;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use SharkMCPE\db\player_yaml;

class EventListener implements Listener
{

    private $plugin;

    public function __construct(Guncha $plugin)
    {
        $this->plugin = $plugin;
    }

    public function Join(PlayerJoinEvent  $event){
        $player = $event->getPlayer();
        $player_db = new player_yaml($this->plugin);
        $player_db->createData($player);
    }
}