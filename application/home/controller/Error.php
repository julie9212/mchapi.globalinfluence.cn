<?php
namespace app\home\controller;
use think\Controller;
use think\Request;
use think\Db;
use think\Exception;
use think\facade\Cache;
use think\facade\Config;

class Error extends Controller
{   
    protected $token = null;

    // protected $captcha = null;

    protected $s_user = null;
    
    public function index()
    {
        return [404,'no found controller'];
    }
    public function _empty($fun)
    {
        return [400,'no such service as '.$fun];
    }
    public function initialize()
    {
    	header("Access-Control-Allow-Origin:*");
        // $this->captcha = new Captcha;
    	$this->validateToken();
    }

    // 登录
    protected function validateToken(){
    	// echo $this->request->url();
    	$rule = Config::get('rule.');

    	if(in_array($this->request->url(),$rule)){
    		$token =input('post.token');
    		if(!$token || !Cache::has($token)){
    			throw new Exception('请登录');
    		}
    		$this->token = $token;
    		$this->s_user = Cache::get($token);
    	}
    }
}