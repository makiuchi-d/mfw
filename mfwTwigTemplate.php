<?php
require_once __DIR__.'/vendor/Twig/lib/Twig/Autoloader.php';
Twig_Autoloader::register();

/**
 * Customized Twig_Environment.
 * - blockとtemplateのディレクトリを分離
 * - テンプレート名にディレクトリセパレータも許容
 * - cacheへの事前変換必須
 */
class mfwTwigEnv extends Twig_Environment {

	const BLOCKMARK = 'block:';

	protected $blockClassPrefix = '__TwigBlock_';
	protected $blockdir;
	protected $templatedir;

    public function __construct(Twig_LoaderInterface $loader = null, $options = array())
	{
		if(!$loader){
			$loader = new Twig_Loader_String();
		}
		parent::__construct($loader,$options);

		$this->blockdir = APP_ROOT.'/data/blocks';
		$this->templatedir = APP_ROOT.'/data/templates';
	}

	public function setBaseDir($dir)
	{
		$dir = rtrim($dir,'/');
		$t = "{$dir}/templates";
		$b = "{$dir}/blocks";
		if(!is_dir($t)||!is_dir($b)){
			return false;
		}
		$this->blockdir = $b;
		$this->templatedir = $t;
		return true;
	}

	protected function isBlock($name){
		return strpos($name,self::BLOCKMARK)===0;
	}
	protected function blockName($name){
		return substr($name,strlen(self::BLOCKMARK));
	}

	/**
	 * @override
	 */
	public function getTemplateClass($name){
		$prefix = $this->templateClassPrefix;
		if($this->isBlock($name)){
			$prefix = $this->blockClassPrefix;
			$name = $this->blockName($name);
		}
		return $prefix.strtr($name,'/','_');
	}

	/**
	 * @override
	 */
	public function getCacheFilename($name)
	{
		if($this->isBlock($name)){
			$name = $this->blockName($name);
			return "{$this->blockdir}/{$name}.php";
		}
		return "{$this->templatedir}/{$name}.php";
	}

	/**
	 * @override
	 */
	public function loadTemplate($name)
	{
		$cls = $this->getTemplateClass($name);
		if(isset($this->loadedTemplates[$cls])){
			return $this->loadedTemplates[$cls];
		}
		require_once $this->getCacheFilename($name);
		if(!$this->runtimeInitialized){
			$this->initRuntime();
		}
		return $this->loadedTemplates[$cls] = new $cls($this);
	}

};

/**
 * Twigを使ったテンプレートエンジン.
 */
class mfwTwigTemplate {

	protected $twig;
	protected $name;

	public function __construct($name,$basedir=null)
	{
		if(!$basedir){
			$basedir = APP_ROOT.'/data';
		}
		$this->twig = new mfwTwigEnv();
		$this->twig->setBaseDir($basedir);
		$this->name = $name;
	}

	public function build($params=array())
	{
		return $this->twig->render($this->name,$params);
	}
}


