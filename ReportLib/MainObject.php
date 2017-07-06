<?php

class MainObject {
    public function setVariables() {
        $this->inVariables = array_merge($this->inVariables,func_get_args());
        $this->formVariables();
    }
    
    public function formVariables() {
        
        foreach ($this->inVariables as $argumento) {
            list($variable,$valor) = explode(":",$argumento);
            $this->$variable = $valor;
        }
        
    }
}