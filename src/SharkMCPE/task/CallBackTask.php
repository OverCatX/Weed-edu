<?php

namespace SharkMCPE\task;

use pocketmine\scheduler\Task;

class CallBackTask extends Task
{

    public function __construct(callable $callable, array $args = []){
        $this->callable = $callable;
        $this->args = $args;
        $this->args[] = $this;
    }

    public function getCallable(){
        return $this->callable;
    }

    public function onRun($currentTicks){
        call_user_func_array($this->callable, $this->args);
    }

}