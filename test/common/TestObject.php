<?php

class TestObject extends mfwObject {
	const DB_CLASS = 'TestObjectDb';
	const SET_CLASS = 'TestObjectSet';

	public function setValue($value){
		$this->row['value'] = $value;
	}

}


class TestObjectSet extends mfwObjectSet {

    const PRIMARY_KEY = 'key';

	public static function hypostatize(Array $row=array())
	{
		return new TestObject($row);
	}

	public function getRows(){
		return $this->rows;
	}
	public function getObjCache(){
		return $this->obj_cache;
	}
}

class TestObjectDb extends mfwObjectDb {
	const TABLE_NAME = 'test_object';
	const SET_CLASS = 'TestObjectSet';

	public static function makeInPlaceholder(Array $list,Array &$bind,$prefix='')
	{
		return parent::makeInPlaceholder($list,$bind,$prefix);
	}
}


