<?php
namespace app\back\controller;

class Index extends BackBase
{

    public function index() {
        return $this->fetch();
    }

    public function teacherIndex() {
        return $this->fetch("index/teacherIndex");
    }

}