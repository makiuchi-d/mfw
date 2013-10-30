<?php

class mfwTemplate
{
	public static $curobj; ///< 現在処理中のTemplate (Template用関数で使用)

	protected $templatefile;
	protected $layoutfile;
	protected $blockdir;
	protected $params;

	/**
	 * @param[in] name テンプレート名
	 * @param[in] basedir テンプレート、ブロックのベースディレクトリ
	 */
	public function __construct($name,$basedir='/data')
	{
		$this->templatedir = APP_ROOT."{$basedir}/templates";
		$this->blockdir = APP_ROOT."{$basedir}/blocks";

		$file = "{$this->templatedir}/{$name}.php";
		if(!file_exists($file)){
			throw new InvalidArgumentException("template file is not exists: {$file}");
		}
		$this->templatefile = $file;

		// default layout file (optional)
		$this->layout = "{$this->templatedir}/_layout.php";
	}

	/**
	 * レイアウトファイルの差し替え.
	 */
	public function setLayout($layout)
	{
		$file = "{$this->templatedir}/{$layout}.php";
		if(!file_exists($file)){
			throw new InvalidArgumentException("layout file is not exists: {$file}");
		}
		$this->layout = $file;
	}

	/**
	 * ページ構築.
	 */
	public function build($params=array())
	{
		$template = file_get_contents($this->templatefile);

		// blockからの呼び出し用に保存
		self::$curobj = $this;
		$this->params = $params;

		$r = new mfwTemplateRenderer($template,$params);
		$contents = $r->render();

		if($this->layout && file_exists($this->layout)){
			$layout = file_get_contents($this->layout);
			$this->params['contents'] = $contents;
			$r = new mfwTemplateRenderer($layout,$this->params);
			$contents = $r->render();
		}

		return $contents;
	}

	public function blockFileName($name)
	{
		return "{$this->blockdir}/{$name}.php";
	}

	public function getParams()
	{
		return $this->params;
	}

}

/**
 * レンダリングクラス.
 * 変数を隔離するためこの中でextractする.
 */
class mfwTemplateRenderer
{
	protected $template;
	protected $params;

	public function __construct($template,$params)
	{
		$this->template = $template;
		$this->params = $params;
	}

	public function render()
	{
		extract($this->params);
		ob_start();
		eval('?>'.$this->template);
		return ob_get_clean();
	}
}



/**
 * Template用関数: URL生成
 */
function url($query,$scheme=null)
{
	return mfwRequest::makeUrl($query,$scheme);
}

/**
 * Template用関数: ブロック読み込み
 */
function block($name,$additional_params=array())
{
	$t = mfwTemplate::$curobj;

	$file = $t->blockFileName($name);

	if(!file_exists($file)){
		return "block '{$name}' is not found.";
	}

	$r = new mfwTemplateRenderer(
		file_get_contents($file),
		$additional_params + $t->getParams());
	return $r->render();
}
