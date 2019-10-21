<?php
namespace Jybtx\OneSignal;

class OneSignalClient
{
	
	const API_URL = "https://onesignal.com/api/v1";

    const ENDPOINT_NOTIFICATIONS = "/notifications";
    const ENDPOINT_PLAYERS = "/players";
    const ENDPOINT_APPS = "/apps";


    protected $appId;
    protected $ApiKe;

    public  function __construct($appId,$ApiKey)
    {
        $this->appId = $appId;
        $this->ApiKey = $ApiKey;
    }

    /**
     * 在注册的时候给用户注册一个消息推送ID
     * @param  [type] $identifier   [来自Google或Apple的推荐推送通知标识符。对于Apple推送标识符，您必须删除所有非字母数字字符]
     * @param  [type] $device_os    [推荐的设备操作系统版本。例：7.0.4]
     * @param  [type] $device_type  [必需设备的平台]
     * @param  [type] $device_model [推荐的设备品牌和型号。例：iPhone5,1]
     * @return [type]               [description]
     */
    public function registerPlayerId($identifier,$device_type,$tags=NULL,$version='1.0',$device_os='',$device_model='')
    {
        $fields = array( 
			'app_id'       => $this->appId, 
			'identifier'   => $identifier, 
			'language'     => config('app.locale'), 
			'timezone'     => config('app.timezone'), 
			'game_version' => $version, 
			'device_os'    => $device_os, 
			'device_type'  => $device_type, 
			'device_model' => $device_model, 
        );
        if ( isset($tags) ) $fields['tags'] = $tags;

        return self::getMethod($fields, self::getUrlMethod( self::ENDPOINT_PLAYERS ) );      
    }
    /**
     * 给所有用户发送消息
     * @param  [string] $title [推送消息标题]
     * @param  [string] $txt   [推送消息主体内容]
     * @return [array]         [推送消息返回数据]
     */
    public function sendMessageAllUsers($title,$txt,$time=NULL,$data = array(),$url = NULL, $buttons = NULL)
    {
        $fields = array(
			'app_id'            => $this->appId,
			'included_segments' => array('All'),
			'send_after'        => $time . config('app.timezone'),
			'headings'          => array('en'=>$title),
			'contents'          => array('en'=>$txt),
        );
        if ( isset( $data ) ) $fields['data'] = $data;
        if ( isset( $url ) ) $fields['url'] = $url;
        if ( isset( $buttons ) ) $fields['buttons'] = $buttons;
        return self::getMethod($fields, self::getUrlMethod( self::ENDPOINT_NOTIFICATIONS ) );
    }
    /**
     * 基于OneSignal PlayerIds发送
     * @author jybtx
     * @date   2019-10-19
     * @param  [string]   $title [推送消息标题]
     * @param  [string]   $txt   [推送消息主体内容]
     * @param  array      $users [要推送消息的人--可以是单人也可以是多人]
     * @param  array      $data  [自定义字段]
     * @return [type]            [description]
     */
    public function sendMessageSomeUser($title,$txt,$users,$data = array(),$url = NULL,$buttons = NULL)
    {
        $fields = array(
			'app_id'             => $this->appId,
			'include_player_ids' => is_array( $users )?:array($users),
			'headings'           => array('en'=>$title),
			'contents'           => array('en'=>$txt),
        );
        if ( isset( $data ) ) $fields['data'] = $data;
        if ( isset( $url ) ) $fields['url'] = $url;
        if ( isset( $buttons ) ) $fields['buttons'] = $buttons;
        return self::getMethod($fields, self::getUrlMethod( self::ENDPOINT_NOTIFICATIONS ) );
    }
    /**
     * 给标签用户发送消息
     * @author jybtx
     * @date   2019-10-21
     * @param  [type]     $title    [description]
     * @param  [type]     $txt      [description]
     * @param  [type]     $tags     [description]
     * @param  [type]     $url      [description]
     * @param  [type]     $data     [description]
     * @param  [type]     $buttons  [description]
     * @param  [type]     $subtitle [description]
     * @return [type]               [description]
     */
    public function sendMessageUsingTags($title,$txt, $tags, $url = NULL, $data = NULL, $buttons = NULL, $subtitle = NULL)
    {
        $fields = array(
            'app_id'   => $this->appId,
            'filters'  => $tags,
            'headings' => array('en'=>$title),
            'contents' => array('en'=>$txt),
        );
        $fields['url']      = $url??'';
        $fields['data']     = $data??'';
        $fields['buttons']  = $buttons??'';
        $fields['subtitle'] = $subtitle??'';
        return self::getMethod($fields, self::getUrlMethod( self::ENDPOINT_NOTIFICATIONS ) );
    }
    /**
     * 查看通知
     * @author jybtx
     * @date   2019-10-21
     * @param  [type]     $limit  [description]
     * @param  [type]     $offset [description]
     * @param  [type]     $kind   [description]
     * @return [type]             [description]
     */
    public function getNotifications($limit = NULL, $offset = NULL, $kind = 1 )
    {
        $fields = array(
            'app_id'   => $this->appId,
        );
        $filters['limit']   = $limit??'';
        $filters['offset '] = $offset??'';
        $filters['offset '] = $offset??'';
        $filters['kind ']   = $kind??'';
        return self::getMethod($fields, self::getUrlMethod( self::ENDPOINT_NOTIFICATIONS ) );
    }
    /**
     * 生成所有当前用户数据的压缩CSV导出
     * @author jybtx
     * @date   2019-10-21
     * @return [type]     [description]
     */
    public function getAllUserToExportCsv()
    {
        $fields = array(
            'app_id'   => $this->appId,
        );
        $url = self::getUrlMethod( self::ENDPOINT_PLAYERS ).'/csv_export';
        return self::getMethod($fields, $url );
    }
    /**
     * 取消通知---已发送的不能取消
     * @param  [id] $notifId   [消息推送onesignal的消息id]
     * @return [array]          [返回错误信息]
     */
    public function revokeMessage($notifId)
    {
        $url = self::getUrlMethod( self::ENDPOINT_NOTIFICATIONS ) .'/'. $notifId."?app_id=".$this->appId;
        return self::getMethod('',$url,'DELETE');
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