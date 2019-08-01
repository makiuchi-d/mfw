<?php
require_once __DIR__.'/Request/mfwUserAgent.php';

/**
 * HTTPリクエストを扱うクラス.
 */
class mfwRequest {

	protected static $pathinfo = null;
	protected static $files = null;
	protected static $body = null;
	protected static $headers  = null;
	protected static $user_agent = null;
	protected static $url = null;
	protected static $url_base = null;
	protected static $this_link_id = null;

	/**
	 * HTTPリクエストメソッドの取得.
	 * @return 'GET', 'POST' など
	 */
	public static function method()
	{
		return strtoupper($_SERVER['REQUEST_METHOD']);
	}

	/**
	 * PATH_INFOを配列で取得.
	 */
	public static function getPathInfoArray()
	{
		if(self::$pathinfo===null){
			if(isset($_SERVER['PATH_INFO'])){
				self::$pathinfo = explode('/',$_SERVER['PATH_INFO']);
				array_shift(self::$pathinfo); // 先頭の空要素を除去
			}
			else{
				self::$pathinfo = array();
			}
		}
		return self::$pathinfo;
	}

	/**
	 * $_FILESを扱いやすい形に変換して返す.
	 * array(
	 *   'file1' => array('name'=>...),
	 *   'file2' => array(
	 *      0 => array('name'=>...),
	 *      1 => array('name'=>...),
	 *      ...
	 *   ),
	 * );
	 */
	protected static function allFiles()
	{
		if(self::$files===null){
			self::$files = array();
			foreach($_FILES as $name => $file){
				if(!is_array($file['error'])){
					if($file['error']===0){
						self::$files[$name] = $file;
					}
				}
				else{
					self::$files[$name] = array();
					foreach($file['error'] as $idx => $err){
						if($err===0){
							self::$files[$name][$idx] = array();
							foreach($file as $k => $v){
								self::$files[$name][$idx][$k] = $v[$idx];
							}
						}
					}
				}
			}
		}
		return self::$files;
	}

	/**
	 * 全パラメータを配列で取得.
	 * @param[in] string $method GET/POST/FILESの指定. 指定なしは$_REQUESTと$_FILESをマージ
	 */
	public static function allParams($method=null)
	{
		switch(strtoupper($method)){
		case 'GET':
			return $_GET;
		case 'POST':
			return $_POST;
		case 'FILES':
			return self::allFiles();
		default:
			return $_REQUEST + self::allFiles();
		}
	}

	/**
	 * クエリパラメータ取得.
	 * @param[in] key パラメータのキー
	 * @param[in] default デフォルト値
	 * @param[in] method  $_GET/$_POST/$_FILES指定. 指定なしは$_REQUESTまたは$_FILESから
	 * @return パラメータの値. そのキーが存在しない時は$default.
	 */
	public static function param($key,$default=null,$method=null)
	{
		switch(strtoupper($method)){
		case 'GET':
			return array_key_exists($key,$_GET)? $_GET[$key]: $default;
		case 'POST':
			return array_key_exists($key,$_POST)? $_POST[$key]: $default;
		case 'FILES':
			$files = self::allFiles();
			return array_key_exists($key,$files)? $files[$key]: $default;
		default:
			if(array_key_exists($key,$_REQUEST)){
				return $_REQUEST[$key];
			}
			$files = self::allFiles();
			return array_key_exists($key,$files)? $files[$key]: $default;
		}
	}

	/**
	 * クエリパラメータの存在確認.
	 * @param[in] key パラメータのキー
	 * @param[in] method  $_GET/$_POST指定. デフォルトは$_REQUEST.
	 * @return 存在するならtrue.
	 * @note
	 *   type=fileの場合、ファイルがアップロードされていなくてもkeyが存在したらtrue.
	 */
	public static function has($key,$method=null)
	{
		switch(strtoupper($method)){
		case 'GET':
			return array_key_exists($key,$_GET);
		case 'POST':
			return array_key_exists($key,$_POST);
		case 'FILES':
			return array_key_exists($key,$_FILES);
		default:
			if(array_key_exists($key,$_REQUEST)){
				return true;
			}
			return array_key_exists($key,$_FILES);
		}
	}

	/**
	 * POST data/PUT data
	 */
	public static function body()
	{
		if(self::$body===null){
			self::$body = file_get_contents('php://input');
		}
		return self::$body;
	}

	/**
	 * 全HTTPリクエストヘッダ取得
	 */
	public static function allHeaders()
	{
		if(self::$headers===null){
			self::$headers = getallheaders();
		}
		return self::$headers;
	}

	/**
	 * HTTPリクエストヘッダの値取得.
	 * @param[in] key キー名.
	 * @return リクエストヘッダの値.
	 */
	public static function header($key)
	{
		$headers = self::allHeaders();
		return isset($headers[$key])? $headers[$key]: null;
	}

	/**
	 * リモートホストのIPアドレス
	 */
	public static function remoteHost()
	{
		return isset($_SERVER['HTTP_X_FORWARDED_FOR'])?
			$_SERVER['HTTP_X_FORWARDED_FOR']: $_SERVER['REMOTE_ADDR'];
	}

	/**
	 * UserAgentオブジェクト取得
	 * @return mfwUserAgentオブジェクト.
	 * @sa class mfwUserAgent.
	 */
	public static function userAgent()
	{
		if(self::$user_agent===null){
			self::$user_agent = new mfwUserAgent();
		}
		return self::$user_agent;
	}

	/**
	 * このリクエストのURL.
	 */
	public static function url()
	{
		if(self::$url===null){
			if(isset($_SERVER['HTTP_X_FORWARDED_PROTO'])){
				$scheme = $_SERVER['HTTP_X_FORWARDED_PROTO'];
			}
			else{
				$scheme = 'http';
				if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']==='on'){
					$scheme = 'https';
				}
			}
			$host = $_SERVER['HTTP_HOST'];
			if(isset($_SERVER['HTTP_X_FORWARDED_HOST'])){
				$host = $_SERVER['HTTP_X_FORWARDED_HOST'];
			}
			self::$url = "{$scheme}://{$host}{$_SERVER['REQUEST_URI']}";
		}
		return self::$url;
	}

	/**
	 * URLを生成.
	 * @param[in] $query     request path (ex: '/hoge/fuga?foo=bar')
	 * @param[in] $scheme  'http' or 'https'
	 * @return 完全なURL (ex: http://example.com/hoge/fuga?foo=bar)
	 */
	public static function makeUrl($query,$scheme=null)
	{
		if(self::$url_base===null){
			$path='';
			if(preg_match('|^(.*)/[^/]+.php|',$_SERVER['SCRIPT_NAME'],$m)){
				$path = $m[1];
			}
			$host = $_SERVER['HTTP_HOST'];
			if(isset($_SERVER['HTTP_X_FORWARDED_HOST'])){
				$host = $_SERVER['HTTP_X_FORWARDED_HOST'];
			}
			self::$url_base = "://{$host}{$path}";
		}
		if(!$scheme){
			if(isset($_SERVER['HTTP_X_FORWARDED_PROTO'])){
				$scheme = $_SERVER['HTTP_X_FORWARDED_PROTO'];
			}
			else{
				$scheme = 'http';
				if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']==='on'){
					$scheme = 'https';
				}
			}
		}
		return $scheme . self::$url_base . $query;
	}

	/**
	 * LinkId.
	 * @{
	 */

	/**
	 * 渡された link_id 取得.
	 * @return link_id
	 */
	public static function linkId()
	{
		return isset($_REQUEST['link_id'])? $_REQUEST['link_id']: null;
	}

	/**
	 * 現在のページのlink_idを生成.
	 * @return link_id
	 */
	public static function makeThisLinkId()
	{
		if(self::$this_link_id===null){
			$url = static::url();
			self::$this_link_id = static::makeLinkId($url);
		}
		return self::$this_link_id;
	}

	/**
	 * 任意のURLのlink_idを生成.
	 * @param[in] $url 戻り先URL
	 * @return link_id
	 */
	public static function makeLinkId($url)
	{
		return mfwMemcache::storeURL($url);
	}

	/**
	 * link_idから戻り先URLを取り出す.
	 * @param[in] $link_id 省略時はクエリパラメータのlink_idが対象
	 * @return URL
	 */
	public static function getReturnUrl($link_id=null)
	{
		if(!$link_id){
			$link_id = static::linkId();
		}
		static $returnurls = array();
		if(!array_key_exists($link_id,$returnurls)){
			$returnurls[$link_id] = mfwMemcache::fetchURL($link_id);
		}
		return $returnurls[$link_id];
	}

	/** @} */

}

