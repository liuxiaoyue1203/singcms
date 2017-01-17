<?php
namespace Common\Model;
use Think\Model;

/**
 * 基本设置
 * @author  singwa
 */
class BasicModel extends Model {

	public function __construct() {

	}

	public function save($data = array()) {
		if(!$data) {
			throw_exception('没有提交的数据');
		}
		// 快速文件数据读取和保存 针对简单类型数据 字符串 数组
        // 默认保存在Application/Runtime/Data/下
		$id = F('basic_web_config', $data);
		return $id;
	}

	public function select() {
		return F("basic_web_config");
	}




}
