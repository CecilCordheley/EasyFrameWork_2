<?php
namespace vendor\easyFrameWork\Core\Master;
abstract class Controller {
   private array $data;
   public function __construct(){
       $this->data=[];
   }
   abstract public function handleRequest();
   public function setData($key,$value){
       $this->data[$key]=$value;
   }
   protected function getData(){return $this->data;}
}