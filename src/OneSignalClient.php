<?php

namespace Jybtx\OneSignal;


class OneSignalClient
{
	
	const API_URL = "https://onesignal.com/api/v1";

    const ENDPOINT_NOTIFICATIONS = "/notifications";
    const ENDPOINT_PLAYERS = "/players";
    const ENDPOINT_APPS = "/apps";


    protected $appId = config('one-sigin.app_id');
    protected $ApiKey = config('one-sigin.api_key');
    protected $userAuthKey;
    protected $additionalParams;

    /**
     * 在注册的时候给用户注册一个消息推送ID
     * @param  [type] $identifier   [来自Google或Apple的推荐推送通知标识符。对于Apple推送标识符，您必须删除所有非字母数字字符]
     * @param  [type] $device_os    [推荐的设备操作系统版本。例：7.0.4]
     * @param  [type] $device_type  [必需设备的平台]
     * @param  [type] $device_model [推荐的设备品牌和型号。例：iPhone5,1]
     * @return [type]               [description]
     */
    public function registerPlayerId($identifier,$device_type,$device_os='',$device_model='')
    {
        $fields = array( 
			'app_id'       => $this->appId, 
			'identifier'   => $identifier, 
			'language'     => config('app.locale'), 
			'timezone'     => config('app.timezone'), 
			'game_version' => "1.0", 
			'device_os'    => $device_os, 
			'device_type'  => $device_type, 
			'device_model' => $device_model, 
			'tags'         => array("foo" => "app") 
        );

        $return = self::getMethod($fields, self::getUrlMethod( self::ENDPOINT_PLAYERS ) );
        
        if ( $return->success != FALSE )
        {
            return $return->id;
        }
        else
        {
            return false;
        }       
    }
    /**
     * 给所有用户发送消息
     * @param  [string] $title [推送消息标题]
     * @param  [string] $txt   [推送消息主体内容]
     * @return [array]         [推送消息返回数据]
     */
    public function sendMessageAllUsers($title,$txt,$time=null)
    {
        $fields = array(
			'app_id'            => $this->appId,
			'included_segments' => array('All'),
			'send_after'        => $time . config('app.timezone'),
			'headings'          => array('en'=>$title),
			'contents'          => array('en'=>$txt),
        );      
        return self::getMethod($fields, self::getUrlMethod( self::ENDPOINT_NOTIFICATIONS ) );
    }
    /**
     * 基于OneSignal PlayerIds发送
     * @param  [string] $title [推送消息标题]
     * @param  [string] $txt   [推送消息主体内容]
     * @param  array  $users [要推送消息的人--可以是单人也可以是多人]
     * @return [array]        [推送消息返回数据]
     */
    public function sendMessageSomeUser($title,$txt,$users=array())
    {
        $fields = array(
			'app_id'             => $this->appId,
			'include_player_ids' => $users,
			'headings'           => array('en'=>$title),
			'contents'           => array('en'=>$txt),
        );        
        return self::getMethod($fields, self::getUrlMethod( self::ENDPOINT_NOTIFICATIONS ) );
    }
    /**
     * 取消通知---已发送的不能取消
     * @param  [id] $notifId   [消息推送onesignal的消息id]
     * @return [array]          [返回错误信息]
     */
    public function revokeMessage($notifId)
    {
        $fields = array(
            'app_id' => $this->appId
        );
        $url = self::getUrlMethod( self::getUrlMethod( self::ENDPOINT_NOTIFICATIONS ) ) . $notifId;
        return self::getMethod($fields,$url,'DELETE');
    }
    /**
     * 请求方法
     * @author jybtx
     * @date   2019-09-29
     * @param  [type]     $fields [description]
     * @param  [type]     $url    [description]
     * @return [type]             [description]
     */
    public function getMethod($fields,$url,$method = NULL)
    {
    	$fields = json_encode($fields);     
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Basic ' . $this->ApiKey
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        if ( $method ) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);        
        $data = json_decode($response, true);
        return $data;
    }
    /**
     * 获取完整的url地址
     * @author jybtx
     * @date   2019-09-29
     * @param  [type]     $endPoint [description]
     * @return [type]               [description]
     */
    public function getUrlMethod($endPoint)
    {
    	return self::API_URL . $endPoint;
    }



}