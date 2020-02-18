<?php
namespace app\index\controller;
use think\Controller;
use think\Request;

class Error extends Controller
{
    public function index()
    {
        return [404,'控制器错误'];
    }
    public function _empty($fun)
    {
        return [400,'no such service as '.$fun];
    }
}