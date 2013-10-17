<?php

/**
 * IDカラムを持つテーブルのレコードのオブジェクト.
 *
 * 派生クラスでは次の定数を定義する。
 * - DB_CLASS: オブジェクトに紐づくDBアクセサクラス名
 * - SET_CLASS: オブジェクトの集合クラス (mfwObjectSet)
 */
abstract class mfwObject {

	protected $row;

	/**
	 * コンストラクタ.
	 * @param[in] $rows 初期化に使う連想配列の配列.
	 */
	public function __construct(Array $row=array())
	{
		$this->fromArray($row);
	}

	/**
	 * 連想配列として取得.
	 */
	public function toArray()
	{
		return $this->row;
	}

	/**
	 * 連想配列で初期化.
	 */
	public function fromArray(Array $row)
	{
		$this->row = $row;
		return $this;
	}

	/**
	 * keyカラムの値.
	 * 定義されていない時はNULL.
	 */
	protected function value($key){
		return isset($this->row[$key])? $this->row[$key]: null;
	}

	/**
	 * primary keyの値.
	 */
	public function getPrimaryKey(){
		$setclass = static::SET_CLASS;
		$pkey = $setclass::PRIMARY_KEY;
		return $this->value($pkey);
	}

	/**
	 * DBへのinsert.
	 * primary keyを設定する.
	 */
	public function insert($con=null)
	{
		$setclass = static::SET_CLASS;
		$pkey = $setclass::PRIMARY_KEY;
		$db = static::DB_CLASS;
		$id = $db::insert($this,$con);
		$this->row[$pkey] = $id;
		return true;
	}

	/**
	 * DBのレコードをupdate
	 */
	public function update($con=null)
	{
		$db = static::DB_CLASS;
		$db::update($this,$con);
	}

	/**
	 * DBから削除.
	 */
	public function delete($con=null)
	{
		if($this->getPrimaryKey()===null){
			return false;
		}
		$db = static::DB_CLASS;
		return $db::delete($this,$con);
	}

}

