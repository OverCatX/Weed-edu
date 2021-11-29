<?php

namespace SharkMCPE\cmd;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use SharkMCPE\form\Form;
use SharkMCPE\Guncha;

class wdCommand extends Command implements PluginIdentifiableCommand
{

    private $plugin;

    public function __construct(Guncha $plugin)
    {
        parent::__construct("wd", "เปิดการตั้งค่า Weed");
        $this->plugin = $plugin;
        $this->setPermission("wd.cmd");
    }

    public function getPlugin(): Plugin
    {
        return $this->plugin;
    }

    public function execute(CommandSender $sender, $commandLabel, array $args)
    {
        if($sender instanceof Player){
            if(empty($args)){
                $form = new Form($this->getPlugin());
                $form->Form($sender);
                return true;
            }
        }
    }

}