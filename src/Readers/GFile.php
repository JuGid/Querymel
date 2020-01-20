<?php

namespace Querymel\Readers;

class GFile{

  private $path;

  public function __construct($path)
  {
    $this->path = $path;
  }

  public function getPath()
  {
    return $this->path;
  }

  public function getName($extension = true)
  {
    return ($extension==true) ? basename($this->path) : basename($this->path,'.php');
  }

  public function isPhpFile()
  {
    return $this->getFileType() == 'php';
  }

  public function getFileType()
  {
    return pathinfo($this->path)['extension'];
  }

  public function asArray()
  {
    return file($this->path);
  }

  public function __toString()
  {
    return 'File : ' . $this->path ;
  }
}
