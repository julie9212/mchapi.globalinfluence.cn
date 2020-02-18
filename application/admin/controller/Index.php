<?php
namespace app\admin\controller;


class Index extends Error
{
    public function indexApi()
    {
        return [501,'数据'];
        
        // return ['ret' => 200 , 'data' => [] ,'msg' => ''];
    }

}
