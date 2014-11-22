<?php
class WeiboOperateAction extends BaseAction{

	protected function _initialize()
	{
		if (!in_array(ACTION_NAME, array('deleteMuleComment'))) {
			parent::_initialize();
			if (!$this->ismember) {
				if (ACTION_NAME == 'transpond') {
					$this->assign('action_name', '转发');
				} else {
					$this->assign('action_name', '发布');
				}
				$this->display('notmember');
				exit;
			}
		}
	}

    //发布
    function publish(){
    	$pWeibo = D('GroupWeibo');
        $data['content'] =  $_POST['content'];
        $data['gid']     =  $this->gid;
        $id = $pWeibo ->publish( $this->mid , $data, 0 ,intval( $_POST['publish_type']) , $_POST['publish_type_data']);
        if( $id ){
        	if ($_POST['myweibo']) {
				$weibo = $pWeibo->find($id);
        		$from_data = array('app_type'=>'local_app', 'app_name'=>'group', 'gid'=>$this->gid, 'title'=>$this->groupinfo['name'], 'url'=>U('group/Group/index', array('gid'=>$this->gid)));
        		$myweibo_id = D('Weibo', 'weibo')->publish( $this->mid , $weibo, 0 ,0 , null, '', serialize($from_data));
        	}
        	//发布成功后，检测后台是否开启了自动举报功能
        	$weibo_option = model('Xdata')->lget('weibo');
        	if( $weibo_option['openAutoDenounce'] && checkKeyWord( $data['content'] ) ){
        		$map['from'] = 'group';
				$map['aid'] = $id;
				$map['uid'] = '0';
				$map['fuid'] = $this->mid;
				$map['content'] = $data['content'];
				$map['reason'] = '内容中含有需要过滤的敏感词';
				$map['ctime'] = time();
				$map['state'] = '1';
        		M( 'Denounce' )->add( $map );
	        	if ($_POST['myweibo']) {
	        		$map['from'] = 'weibo';
					$map['aid'] = $myweibo_id;
	        		M( 'Denounce' )->add( $map );
	        	}
        		echo '0';exit;
        	}
			X('Credit')->setUserCredit($this->mid,'add_weibo');
        	$data = $pWeibo->getOneLocation($id, $this->gid);
        	$this->assign('data',$data);
        	$this->display();
        }
    }

    // 转发
    function transpond()
    {
    	$pWeibo = D('GroupWeibo');
    	if ($_POST) {
	        $post['gid']         	 = $this->gid;
	        $post['content']         = $_POST['content'];
	        $post['transpond_id']    = intval( $_POST['transpond_id'] );
	        $post['reply_weibo_id']  = $_POST['reply_weibo_id'];
	        if ($id = $pWeibo->transpond($this->mid, $post)) {
	        	$data = $pWeibo->getOneLocation($id, $this->gid);
				X('Credit')->setUserCredit($this->mid,'forward_weibo')
						   ->setUserCredit($data['expend']['uid'],'forwarded_weibo');
        		$this->assign('data',$data);
        		$this->display('publish');
	        }
    	} else {
	    	$intId = intval( $_GET['id'] );
	    	$info = $pWeibo->where( 'weibo_id=' . $intId . ' AND gid=' . $this->gid)->find();
	    	if ($info['transpond_id']) {
	    		$info['transponInfo'] = D('WeiboOperate')->field('weibo_id,uid,content')
	    												 ->where('weibo_id=' . $info['transpond_id'] . ' AND gid=' . $this->gid)
	    												 ->find();
	    	} else {
	    		$info['old_content'] = $info['content'];
	    	}
	    	$info['upcontent'] = intval($_GET['upcontent']);
	    	$this->assign( 'data' , $info );
	    	$this->display();
    	}
    }

    // 分享微博
    function shareWeibo()
    {
    	$pWeibo = D('GroupWeibo');
	    $intId = intval( $_GET['id'] );
	    $info = $pWeibo->where( 'weibo_id=' . $intId . ' AND gid=' . $this->gid)->find();
	    if ($info['transpond_id']) {
	    	$info['transponInfo'] = D('WeiboOperate')->field('weibo_id,uid,content,type,type_data')
	    											 ->where('weibo_id=' . $info['transpond_id'] . ' AND gid=' . $this->gid)
	    											 ->find();
	    	$info['transponInfo']['type_data'] = unserialize($info['transponInfo']['type_data']);
	    } else {
	    	$info['type_data'] = unserialize($info['type_data']);
	    }
	    $this->assign( 'data' , $info );
	    $this->display();
    }

	// 分享到微博
	public function weibo() {
		// 解析参数
		$_GET['param']	= unserialize(urldecode($_GET['param']));
		$active_field	= $_GET['param']['active_field'] == 'title' ? 'title' : 'body';
		$this->assign('has_status', $_GET['param']['has_status']);
		$this->assign('is_success_status', $_GET['param']['is_success_status']);
		$this->assign('status_title', t($_GET['param']['status_title']));

		// 解析模板(统一使用模板的body字段)
		$_GET['data']	= unserialize(urldecode($_GET['data']));
		$content		= model('Template')->parseTemplate(t($_GET['tpl_name']), array($active_field=>$_GET['data']));
		//$content		= preg_replace_callback('/((?:https?|ftp):\/\/(?:www\.)?(?:[a-zA-Z0-9][a-zA-Z0-9\-]*\.)?[a-zA-Z0-9][a-zA-Z0-9\-]*(?:\.[a-zA-Z0-9]+)+(?:\:[0-9]*)?(?:\/[^\x{4e00}-\x{9fa5}\s<\'\"“”‘’]*)?)/u',group_get_content_url, $content);
		$this->assign('content', $content[$active_field]);

		$this->assign('type',$_GET['data']['type']);
		$this->assign('type_data',$_GET['data']['type_data']);
		$this->assign('button_title', t(urldecode($_GET['button_title'])));
		$this->display();
	}

	// 分享到微博
	public function doShare()
	{
	    $data['gid']     = $this->gid;
		$data['content'] = $_POST['content'];
		$type      = intval($_POST['type']);
		$type_data = $_POST['typedata'];

        $id = D('GroupWeibo','group')->publish($this->mid, $data, 0, $type, $type_data, '', $from_data);

        if ($id) {
        	X('Credit')->setUserCredit($this->mid,'share_to_weibo');
        	echo '1';
        } else {
        	echo '0';
        }
	}

    //添加评论
    function addcomment()
    {
    	$post['reply_comment_id'] = $_POST['reply_comment_id'];   //回复 评论的ID
    	$post['weibo_id']         = $_POST['weibo_id'];           //回复 微博的ID
    	$post['gid']         	  = $this->gid;           		  //群组的ID
    	$post['content']          = $_POST['comment_content'];    //回复内容
    	$post['transpond']        = $_POST['transpond'];          //是否同是发布一条微博
		echo D('WeiboComment')->doaddcomment($this->mid, $post);
		if(intval($_POST['transpond_weibo_id'])){//同时评论给原文作者
			unset($post['reply_comment_id']);
			unset($post['transpond']);
			$post['weibo_id'] = $_POST['transpond_weibo_id'];
			D('WeiboComment')->doaddcomment($this->mid, $post, true);
		}
    }

    //删除评论
    function docomments(){
    	$result = D('WeiboComment')->deleteComments( $_POST['id'] , $this->mid);
    	echo json_encode($result);
    }

    //批量删除评论
    function deleteMuleComment(){
    	$result = D('WeiboComment', 'group')->deleteMuleComments( $_POST['id'] , $this->mid);
    	echo json_encode($result);
    }

    //删除微博
    function delete(){
    	$arrWeiInfo = D('WeiboOperate')->where('weibo_id=' . $_POST['id'] . ' AND gid=' . $this->gid)->field('isdel')->find();
    	if(!$arrWeiInfo['isdel']){
	    	if( D('WeiboOperate')->deleteMini(intval($_POST['id']), $this->gid, $this->mid)){
				X('Credit')->setUserCredit($this->mid, 'delete_weibo');
	    		echo '1';
	    	}
    	}else{
    		echo '1';
    	}
    }

    //关注话题
    function followtopic(){
    	$name = $_POST['name'];
    	$topicId = D('WeiboTopic')->getTopicId($name);
    	if($topicId){
    		$id = D('WeiboFollow')->dofollow($this->mid,$topicId,1);
    	}
    	echo json_encode(array('code'=>$id,'topicId'=>$topicId,'name'=>h(t(mStr(preg_replace("/#/",'',$name),150,'utf-8',false)))));
    }

    //取消关注话题
    function unfollowtopic(){
        $topicId = intval($_POST['topicId']);
    	if($topicId){
    		$id = D('WeiboFollow')->unfollow($this->mid,$topicId,1);
    	}
    	echo $id;
    }

    //上传图片
    function uploadpic(){

    }

    function quickpublish(){
    	$this->assign('text', $_POST['text'] );
    	$this->display();
    }

    //上传临时文件

    // 预同步 (如果已绑定过, 自动同步; 否则展示"开始绑定"按钮)
    function beforeSync() {
    	if ( !in_array($_GET['type'], array('sina')) ) {
    		echo 0;
    	}

    	// 展示"开始绑定"按钮
    	$map['uid']  = $this->mid;
    	$map['type'] = 'sina';
   		if( M('login')->where("uid={$this->mid} AND type='{$_GET['type']}' AND oauth_token<>''")->count() ){
   			M('login')->setField('is_sync',1,$map);
   			echo '1';
   		}else{
   			$_SESSION['weibo_bind_target_url'] = U('home/User/index');
   			$this->assign('url', U('weibo/Operate/bind',array('type'=>$_GET['type'])));
   			$this->display();
   		}
    }

    //绑定帐号
    function bind() {
    	if ( !in_array($_GET['type'], array('sina')) ) {
    		if ($this->isAjax()) {
    			echo 0;
    			exit;
    		}else {
    			$this->error('参数错误');
    		}
    	}
    	include_once SITE_PATH."/addons/plugins/login/{$_GET['type']}.class.php";
		$platform = new $_GET['type']();
		$call_back_url = U("weibo/Operate/bind{$_GET['type']}CallBack");
		$url = $platform->getUrl($call_back_url);
		redirect($url);
    }

    function bindSinaCallBack() {
    	include_once( SITE_PATH.'/addons/plugins/login/sina.class.php' );
		$sina = new sina();
    	$sina->checkUser();

    	if ( !in_array($_SESSION['open_platform_type'], array('sina')) ) {
    		if ($this->isAjax()) {
				echo 0;
				exit;
    		}else {
    			$this->assign('jumpUrl', U('home/Account/bind').'#sina');
    			$this->error('授权失败');
    		}
		}

		// 检查是否成功获取用户信息
		$userinfo = $sina->userInfo();
		if ( !is_numeric($userinfo['id']) || !is_string($userinfo['uname']) ) {
			$this->assign('jumpUrl', U('home/Account/bind').'#sina');
			$this->error('获取用户信息失败');
		}

		$syncdata['uid']                = $this->mid;
		$syncdata['type_uid']           = $userinfo['id'];
		$syncdata['type']               = 'sina';
		$syncdata['oauth_token']        = $_SESSION['sina']['access_token']['oauth_token'];
		$syncdata['oauth_token_secret'] = $_SESSION['sina']['access_token']['oauth_token_secret'];
		$syncdata['is_sync']			= '1';
		if ( $info = M('login')->where("type_uid={$userinfo['id']} AND type='sina'")->find() ) {
			// 该新浪用户已在本站存在, 将其与当前用户关联(即原用户ID失效)
			M('login')->where("`login_id`={$info['login_id']}")->save($syncdata);
		}else {
			// 添加同步信息
			M('login')->add($syncdata);
		}

		if ( isset($_SESSION['weibo_bind_target_url']) ) {
			$this->assign('jumpUrl', $_SESSION['weibo_bind_target_url']);
			unset($_SESSION['weibo_bind_target_url']);
		}else {
			$this->assign('jumpUrl', U('home/Account/bind').'#sina');
		}
		$this->success('绑定成功');
    }

    /**
     * @deprecated
     */
    function bind_backup(){
    	$type = h($_POST['value']);
    	if($_POST){
	    	include_once( SITE_PATH.'/addons/plugins/login/sina.class.php' );
			$sina = new sina();
			$weiboAuth =   $sina->getJSON($_POST['username'],$_POST['password']);
			if( $weiboAuth['oauth_token'] ){
				$data['type']     = 'sina';
				$data['type_uid'] =  $weiboAuth['user_id'];
				$data['uid']      = $this->mid;
				if($info = M('login')->where($data)->find()){
					if($info['oauth_token']){
						M('login')->setField('is_sync',1,$data);
					}else{
						$savedata['oauth_token'] 		= $weiboAuth['oauth_token'];
						$savedata['oauth_token_secret'] = $weiboAuth['oauth_token_secret'];
						$savedata['is_sync'] = 1;
						M('login')->where('login_id='.$info['login_id'])->data($savedata)->save();
					}
				}else{
					$data['oauth_token'] 		= $weiboAuth['oauth_token'];
					$data['oauth_token_secret'] = $weiboAuth['oauth_token_secret'];
					$data['is_sync'] = 1;
					M('login')->add($data);
				}
				echo '1';
			}else{
				echo '0';
			}
    	}else{
    		$map['uid'] = $this->mid;
    		$map['type'] = 'sina';
    		if( M('login')->where("uid={$this->mid} AND type='sina' AND oauth_token<>''")->count() ){
    			M('login')->setField('is_sync',1,$map);
    			echo '1';
    		}else{
    			$this->display();
    		}
    	}
    }

    //绑定email
    function bindemail(){
    	$email = $_POST['email'];
    	$passwd = $_POST['passwd'];
		if (!preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email)){
			$return['boolen'] = false;
    		$return['message'] = '邮箱格式错误';
    		exit(json_encode($return));
		}
    	if( M('user')->where("email='{$email}'")->count() ){
    		$return['boolen'] = false;
    		$return['message'] = '邮箱已存在';
    		exit(json_encode($return));
    	}

    	$data['email']    = $email;
    	$data['password'] = md5($passwd);
    	if( M('user')->where('uid='.$this->mid)->data($data)->save() ){
    		$return['boolen'] = true;
    		exit(json_encode($return));
    	}else{
    		$return['boolen'] = false;
    		$return['message'] = '绑定失败';
    		exit(json_encode($return));
    	}

    }

    //取消绑定
    function delbind(){
    	if( M('login')->where("uid={$this->mid} AND type='sina'")->delete() ){
    		echo '1';
    	}else{
    		echo '0';
    	}
    }

    function unbind(){
    	$type = h($_POST['value']);
    	echo M("login")->setField('is_sync',0,"uid={$this->mid} AND type='{$type}'" );
    }
}
?>