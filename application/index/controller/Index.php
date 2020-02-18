<?php
namespace app\index\controller;


class Index extends Error
{
    public function index()
    {
        return [501,'数据'];
        // return ['ret' => 200 , 'data' => [] ,'msg' => ''];
    }
    public function indexs()
    {
        return [200,'ok'];
        // return ['ret' => 200 , 'data' => [] ,'msg' => ''];
    }


}
