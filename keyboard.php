<?php

class Button{
    private $action;
    public $color;
    private $button;
    function __construct($num, $label, $color = 'secondary', $type = 'text') {
        (string)$num;
        $this -> action= array(
            'type'=> $type,
            'payload' => json_encode(['button'=>(string)$num],JSON_UNESCAPED_UNICODE),
            'label' => $label);
        $this->color = $color;
        $this -> button= array('action' => $this->action,
            'color'=> $this -> color);
             
    }
    public function getButton(){
        return $this-> button;
    }
}

class Keyboard{
    private $row =1;
    private $col=[1];
    public $offset = 1000;
    public $color = 'secondary';
    public $labels = array();
    public $colors = array();
    public $numbers = array();
    public $one_time = true;
    public $hide = false;
    
    
    public function __construct($show = true) {
        if(!$show){
            $this->row=0;
            $this->col =[0];
            $this->hide = true;
        }
    }

    public function setParam($param){
        if(!$this->hide){
            $this->row = $param['row'];
            $this->col = $param['col'];
            $this->labels = $param['labels'];
            $this->colors = $param['colors'];
            $this->one_time = $param['one_time'];
            $this->offset = $param['offset'];
            $this->numbers = $param['numbers'];
        }
        return;
    }

    public function getKeyboard(){
        
        if (empty($this->colors)){
            self::getDefaultColors();
        }
        if(empty($this->labels)){
            self::getEmptyLabels();
        }
        $keyboard = [
            'one_time' => $this->one_time,
            'buttons' => self::getButtonsArray()
        ];
        return json_encode($keyboard, JSON_UNESCAPED_UNICODE);
    }
    
    
    private function getButtonsArray(){//building the array "buttons"
        $num =0;
        $buttons = array();
        for($i=0; $i<$this->row; $i++){
            $button_str = array();
            for($j=0; $j<$this->col[$i]; $j++){
                $number = self::getNum($num);
                $label = self::getLabel($num);
                $color = self::getColor($num);
                $key = new Button($number, $label, $color);
                $object = $key->getButton();
                array_push($button_str, $object);
                $num ++;
            }
            array_push($buttons, $button_str);
        }
        return $buttons;
    }
    
    
    private function getNum($num){
        if (empty($this->numbers[$num])){
                    $number = $this->offset + 900 + $num;
        }
        else{
            $number = $this->numbers[$num];
        }
        return $number;
    }
    
    
    private function getLabel($num){
        if (empty($this->labels[$num])){
                    $label = 'empty';
        }
        else{
            $label = $this->labels[$num];
        }
        return $label;
    }
    
    
    
    
    private function getColor($num){
        if (empty($this->colors[$num])){
                    $color = 'secondary';
        }
        else{
            $color = $this->colors[$num];
        }
        return $color;
    }
    
    
    private function getDefaultColors() {//Fill an array by defaults
        $sum=0;
        foreach($this->col as $col){
            $sum += $col;
        }
        for($i=0; $i<$this->row*$sum; $i++){
            array_push($this->colors,'secondary');
        }
    }
    
    
    private function getEmptyLabels() {//Fill an array by defaults
        $sum=0;
        foreach($this->col as $col){
            $sum += $col;
        }
        for($i=0; $i<$this->row*$sum; $i++){
            array_push($this->labels,'empty');
        }
    }  
}

