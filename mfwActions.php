<?php

class mfwActions {

	const HTTP_200_OK = "HTTP/1.1 200 OK";
	const HTTP_201_CREATED = "HTTP/1.1 201 Created";
	const HTTP_202_ACCEPTED = "HTTP/1.1 202 Accepted";
	const HTTP_301_MOVEDPERMANENTLY = "HTTP/1.1 301 Moved Permanently";
	const HTTP_302_FOUND = "HTTP/1.1 302 Found";
	const HTTP_400_BADREQUEST = "HTTP/1.1 400 Bad Request";
	const HTTP_401_UNAUTHORIZED = "HTTP/1.1 401 Unauthorized";
	const HTTP_403_FORBIDDEN = "HTTP/1.1 403 Forbidden";
	const HTTP_404_NOTFOUND = "HTTP/1.1 404 Not Found";
	const HTTP_405_METHODNOTALLOWED = "HTTP/1.1 405 Method Not Allowed";
	const HTTP_500_INTERNALSERVERERROR = "HTTP/1.1 500 Internal Server Error";
	const HTTP_503_SERVICEUNAVAILABLE = "HTTP/1.1 503 Service Unavailable";


	protected $module = null;
	protected $action = null;
	protected $templatename = null;
	protected $templatecls = 'mfwTemplate';

	public function __construct($module,$action)
	{
		$this->module = $module;
		$this->action = $action;
	}

	/**
	 * アクション初期化.
	 * execute*()の前に呼ばれる。Actions共通の初期化処理を書く.
	 * @return error responce. エラー無しならnull.
	 *         ex: return $this->redirect(...)
	 */
	public function initialize()
	{
		return null;
	}

	public function getModule()
	{
		return $this->module;
	}

	public function getAction()
	{
		return $this->action;
	}

	protected function setTemplateName($templatename)
	{
		$this->templatename = $templatename;
	}
	protected function setTemplateClass($templatecls)
	{
		$this->templatecls = $templatecls;
	}

	protected function build($params=array(),$headers=array())
	{
		if(empty($this->templatename)){
			$this->setTemplateName("{$this->getModule()}/{$this->getAction()}");
		}

		$template = new $this->templatecls($this->templatename);
		$content = $template->build($params);

		return array($headers,$content);
	}

	protected function redirect($query,$params=array())
	{
		$query = mfwHttp::composeUrl($query,$params);

		if(strpos($query,'://')===false){
			$query = mfwRequest::makeUrl($query);
		}

		$headers = array(
			"Location: $query",
			);
		return array($headers,null);
	}

	public function executeDefaultAction()
	{
		$headers = array(
			self::HTTP_404_NOTFOUND,
			);
		$content = '404 Not Found';
		return array($headers,$content);
	}
}

