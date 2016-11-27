<?php
/**
 * Author: Lying
 * Data: 2016-11-05
 * description: 扩展模型
 */
class IrisModel extends Model{
	protected $table = 'iris';

	public function get_all(){
		return $this->all();
	}
}
?>