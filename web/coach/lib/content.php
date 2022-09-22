<?php
class Content{
  private $id = null;
  private $category_id = null;
  private $points = null;
  private $why = null;
  private $future = null;
  private $filename = null;  
  private $file = null;
  private $what = null;
  private $step = null;
  private $created_at = null;
  private $updated_at = null;

  public function save(){
    $queryContent = new QueryContent();
    $queryContent->setContent($this);
    $queryContent->save();
  }

  public function delete(){
    $queryContent = new QueryContent();
    $queryContent->setContent($this);
    $queryContent->delete();
  }

  public function getId(){
    return $this->id;
  }

  public function getCategoryId(){
    return $this->category_id;
  }

  public function getPoints(){
    return $this->points;
  }

  public function getWhy(){
    return $this->why;
  }

  public function getFuture(){
    return $this->future;
  }
  
  public function getFilename(){
    return $this->filename;
  }

  public function getFile(){
    return $this->file;
  }

  public function getWhat(){
    return $this->what;
  }

  public function getStep(){
    return $this->step;
  }

  public function getCreatedAt(){
    return $this->created_at;
  }

  public function getUpdatedAt(){
    return $this->updated_at;
  }

  public function setId($id){
    $this->id = $id;
  }

  public function setCategoryId($category_id){
    $this->category_id = $category_id;
  }

  public function setPoints($points){
    $this->points = $points;
  }

  public function setWhy($why){
    $this->why = $why;
  }

  public function setFuture($future){
    $this->future = $future;
  }

  public function setFilename($filename){
    $this->filename = $filename;
  }

  public function setFile($file){
    $this->file = $file;
  }
  
  public function setWhat($what){
    $this->what = $what;
  }

  public function setStep($step){
    $this->step = $step;
  }

  public function setCreatedAt($created_at){
    $this->created_at = $created_at;
  }

  public function setUpdatedAt($updated_at){
    $this->updated_at = $updated_at;
  }
}
