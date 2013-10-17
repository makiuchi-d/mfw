<?php

/**
 */
class MockFunction {

	protected static $counter = array();
	protected static $retvals = array();
	protected static $evals = array();

	public static function invoke($funcname,$args)
	{
		$counter = self::$counter[$funcname];
		$retval = self::$retvals[$funcname][$counter];
		if(self::$evals[$funcname][$counter]){
			if(is_callable($retval)){
				$retval = call_user_func_array($retval,$args);
			}
			elseif(is_string($retval)){
				$retval = eval("return \"$retval\";");
			}
		}
		return $retval;
	}

	/**
	 * replace to mock.
	 * @param string $funcname  Function name replaced to mock.
	 * @param Mixed  $retval    Return value of mock.
	 * @param bool   $eval      When this is true, evaluate $retval.
	 *
	 * Evaluation:
	 *  $retval is callable, mocking function calls $retval with
	 *  same arguments of mocked function.
	 *  $retval is uncallable string, "$args" is parsed as
	 *  array of mocked function parameters.
	 */
	public static function replace($funcname,$retval,$eval=false)
	{
		if(!isset(self::$counter[$funcname])){
			self::$counter[$funcname] = 0;
			runkit_function_rename($funcname,"__original__{$funcname}");
			runkit_function_add(
				$funcname,'',
				"return MockFunction::invoke('$funcname',func_get_args());");
		}
		self::$counter[$funcname] += 1;
		self::$retvals[$funcname][self::$counter[$funcname]] = $retval;
		self::$evals[$funcname][self::$counter[$funcname]] = $eval;
	}

	/**
	 * restore mocked function.
	 * @param string $funcname  Function name to restore.
	 */
	public static function restore($funcname)
	{
		if(!isset(self::$counter[$funcname])){
			return;
		}
		$count = self::$counter[$funcname];
		self::$counter[$funcname] -= 1;
		unset(self::$retvals[$count]);
		unset(self::$evals[$count]);
		if(self::$counter[$funcname]==0){
			unset(self::$counter[$funcname]);
			unset(self::$retvals[$funcname]);
			unset(self::$evals[$funcname]);
			runkit_function_remove($funcname);
			runkit_function_rename("__original__{$funcname}",$funcname);
		}
	}
}

