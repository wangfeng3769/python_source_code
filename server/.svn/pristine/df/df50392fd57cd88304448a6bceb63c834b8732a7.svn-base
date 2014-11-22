<?php
/**
 * 用户组模型
 * 
 * @author daniel <desheng.young@gmail.com>
 */
class VipCardModel extends Model {
	protected	$tableName	=	'vip_card';
	protected $tablePrefix  =   'edo_cp_';

	/**
	 * 删除卡号
	 * 
	 * @param string $gids 用户会员卡
	 * @return boolean
	 */
	public function deleteVipCard($id) {
		//防误操作
		if (empty($id)) return false;
		$map['id']	= array('in', $id);
		M('user_group')		->where($map)->delete();
		//M('user_group_link')->where($map)->delete();
		return true;
	}
	
	/**
	 * 获取所有的会员卡信息
	 * 
	 * 本方法按照运行时缓存->文件缓存->数据库的顺序查询
	 * 
	 * @param $do_format 是否将结果集格式化为array($user_group_id => $user_group)
	 */
	public function getAllVipCard($do_format = true) {
		$cache_id = '_model_vip_card' . ($do_format ? '_1' : '_0');
		//echo "getAllVipCard";
		if (($res = object_cache_get($cache_id)) === false) {
			if (($res = F($cache_id)) === false) {
				$temp = $this->findAll();
				if ($do_format) {
					foreach ($temp as $v)
						$res[$v['id']] = $v;
				}else {
					$res = $temp;
				}
				unset($temp);
				
				F($cache_id, $res);
			}
			
			object_cache_set($cache_id, $res);
		}
		return $res;
	}
	

	/**
	 * 按照查询条件获取用户组
	 * 
	 * @param array  $map   查询条件
	 * @param string $field 字段 默认*
	 * @param string $order 排序 默认 以用户组ID升序排列
	 * @return array 用户组信息
	 */
	public function getVipCardByMap($map = '', $field = '*', $order = 'id ASC') {
		return $this->field($field)->where($map)->order($order)->findAll();
	}


	/**
	 * 根据IDs获取会员卡信息
	 * 
     * @param array  $gids  会员卡ID
     * @param string $field 字段 默认*
     * @param string $order 排序 默认空
     * @return array 用户组信息
	 */
	public function getVipCardById($ids, $field = '*', $order = '') {
		$map['id']	= array('in', $ids);
		return $this->getVipCardByMap($map, $field, $order);
	}

	/**
	 * 根据卡号获取会员卡信息
	 * 
     * @param array  $gids  会员卡ID
     * @param string $field 字段 默认*
     * @param string $order 排序 默认空
     * @return array 用户组信息
	 */
	public function getVipCardByCardNumber($numbers, $field = '*', $order = '') {
		$map['card_number']	= array('in', $numbers);
		return $this->getVipCardByMap($map, $field, $order);
	}
	
    /**
     * 检测用户组是否存在
     * 
     * @param unknown_type $title 用户组名称
     * @param unknown_type $gid   用户组ID 该函数里为非该用户组ID
     * @return boolean
     */
	public function isUserGroupExist($title, $gid = 0) {
		$map['user_group_id']	= array('neq', $gid);
		$map['title']			= $title;
    	return M('user_group')->where($map)->find();
    }

    /**
     * 根据用户ID获取该用户所在用户组的ID
     *
     * @param unknown_type $uid
     * @return array $gid
     */
    public function getUserGroupId($uid){
    	if(($gid = S('UserGroupIds_'.$uid)) === false){
	    	$map['uid']	= $uid;
	    	$gid = array();
	    	if($list = M('user_group_link')->where($map)->field('user_group_id')->findAll()){
	    		foreach($list as $v){
	    			$gid[] = $v['user_group_id'];
	    		}
	    	}
	    	S('UserGroupIds_'.$uid,$gid);
    	}
    	return $gid;
    }

}