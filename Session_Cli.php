<?php
/**
 * 模拟命令行下的session
 * 此暂时只保存一条记录
 */
class Session_Cli
{
    private $_path;

    protected static $_instance = null; 

    private function __construct() {}

    private function __clone() {}

    public static function getInstance()
    {
        if(!is_null(self::$_instance)){
            return self::$_instance;
        }else{
            $instance = new self;
            $instance->_path = dirname(__FILE__).'/session';    
            self::$_instance = $instance;   
            return $instance;
        }
    }
    public function set($key, $val)
    {
        $json = json_encode([$key => $val]);
        file_put_contents($this->_path, serialize($json));
        return true;
    }

    public function get($key)
    {
        $session = $this->getContents();
        if(isset($session[$key])){
            return $session[$key];
        }else{
            return null;
        }
    }

    public function getContents()
    {
        $content = file_get_contents($this->_path);
        return json_decode(unserialize($content),true);
    }
    
}
?>