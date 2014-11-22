<?php

/**
 * ECSHOP 车辆管理语言文件
 * ============================================================================
 * 版权所有 2005-2010 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: category.php 17063 2010-03-25 06:35:46Z liuhui $
*/

/* 车辆字段信息 */
$_LANG['car_id'] = '编号';
$_LANG['car_name'] = '车辆昵称';
$_LANG['car_number'] = '车牌号';

$_LANG['workday'] = '工作日';
$_LANG['workday_price'] = '工作日基本价';
$_LANG['holiday'] = '节假日';
$_LANG['hour_price'] = '时租价格 (/小时)';
$_LANG['hour_contain_km'] = '小时租金含公里数';
$_LANG['price_km'] = '里程价格 (/公里)';
$_LANG['top_price_24'] = '24小时封顶价' ;
$_LANG['top_km_24'] = '24小时封顶价含公里数' ;
$_LANG['price_time_out'] = '超时价格 (/30 分钟)' ;

$_LANG['base_table'] = '基本计价表';
$_LANG['detail_table'] = '详细小时计价表';
$_LANG['top_table'] = '时段封顶价';
//小时租金 (/小时) 小时租金 含公里数 24 小时 封顶价  24 小时 封顶价 含公里数 公里价格 (/公里) 超时价格 (/30 分钟)
//区间,价格,含公里数
$_LANG['grade'] = '区间';
$_LANG['price'] = '价格';
$_LANG['add'] = '增加区间';
//时段 生效日期 失效日期 起始时间 终止时间 时段封顶价 含公里数
$_LANG['period'] = '时段';
$_LANG['date_from']='生效日期';
$_LANG['date_end'] = '失效日期';
$_LANG['time_from'] = '起始时间';
$_LANG['time_end'] = '终止时间';
$_LANG['period_top'] = '时段封顶价';
$_LANG['contain_km'] = '含公里数';
//时段 生效日期 失效日期 起始时间 终止时间 时段封顶价 含公里数



$_LANG['isleaf'] = '不允许';
$_LANG['noleaf'] = '允许';
$_LANG['keywords'] = '关键字';
$_LANG['cat_desc'] = '车辆描述';
$_LANG['parent_id'] = '上级车辆';
$_LANG['sort_order'] = '排序';
$_LANG['measure_unit'] = '数量单位';
$_LANG['delete_info'] = '删除选中';
$_LANG['category_edit'] = '编辑车辆';
$_LANG['move_goods'] = '转移商品';
$_LANG['cat_top'] = '顶级车辆';
$_LANG['show_in_nav'] = '是否显示在导航栏';
$_LANG['cat_style'] = '车辆的样式表文件';
$_LANG['is_show'] = '是否显示';
$_LANG['show_in_index'] = '设置为首页推荐';
$_LANG['notice_show_in_index'] = '该设置可以在首页的最新、热门、推荐处显示该车辆下的推荐商品';
$_LANG['goods_number'] = '商品数量';
//$_LANG['grade'] = '价格区间个数';
$_LANG['notice_grade'] = '该选项表示该车辆下商品最低价与最高价之间的划分的等级个数，填0表示不做分级，最多不能超过10个。';
$_LANG['short_grade'] = '价格分级';

$_LANG['nav'] = '导航栏';
$_LANG['index_new'] = '最新';
$_LANG['index_best'] = '精品';
$_LANG['index_hot'] = '热门';

$_LANG['back_list'] = '返回定价模板列表';
$_LANG['continue_add'] = '继续添加模板';

$_LANG['notice_style'] = '您可以为每一个车辆指定一个样式表文件。例如文件存放在 themes 目录下则输入：themes/style.css';

/* 操作提示信息 */
$_LANG['catname_empty'] = '车辆昵称不能为空!';
$_LANG['catname_exist'] = '已存在相同的车辆昵称!';
$_LANG["parent_isleaf"] = '所选车辆不能是末级车辆!';
$_LANG["cat_isleaf"] = '不是末级车辆或者此车辆下还存在有商品,您不能删除!';
$_LANG["cat_noleaf"] = '底下还有其它子车辆,不能修改为末级车辆!';
$_LANG["is_leaf_error"] = '所选择的上级车辆不能是当前车辆或者当前车辆的下级车辆!';
$_LANG['grade_error'] = '价格分级数量只能是0-10之内的整数';

$_LANG['catadd_succed'] = '新车辆添加成功!';
$_LANG['catedit_succed'] = '车辆编辑成功!';
$_LANG['catdrop_succed'] = '车辆删除成功!';
$_LANG['catremove_succed'] = '车辆转移成功!';
$_LANG['move_cat_success'] = '转移车辆已成功完成!';

$_LANG['cat_move_desc'] = '什么是转移车辆?';
$_LANG['select_source_cat'] = '选择要转移的车辆';
$_LANG['select_target_cat'] = '选择目标车辆';
$_LANG['source_cat'] = '从此车辆';
$_LANG['target_cat'] = '转移到';
$_LANG['start_move_cat'] = '开始转移';
$_LANG['cat_move_notic'] = '在添加商品或者在商品管理中,如果需要对商品的车辆进行变更,那么你可以通过此功能,正确管理你的车辆。';

$_LANG['cat_move_empty'] = '你没有正确选择车辆!';

$_LANG['sel_goods_type'] = '请选择商品类型';
$_LANG['sel_filter_attr'] = '请选择筛选属性';
$_LANG['filter_attr'] = '筛选属性';
$_LANG['filter_attr_notic'] = '筛选属性可在前车辆页面筛选商品';
$_LANG['filter_attr_not_repeated'] = '筛选属性不可重复';

/*JS 语言项*/
$_LANG['js_languages']['catname_empty'] = '车辆昵称不能为空!';
$_LANG['js_languages']['unit_empyt'] = '数量单位不能为空!';
$_LANG['js_languages']['is_leafcat'] = '您选定的车辆是一个末级车辆。\r\n新车辆的上级车辆不能是一个末级车辆';
$_LANG['js_languages']['not_leafcat'] = '您选定的车辆不是一个末级车辆。\r\n商品的车辆转移只能在末级车辆之间才可以操作。';
$_LANG['js_languages']['filter_attr_not_repeated'] = '筛选属性不可重复';
$_LANG['js_languages']['filter_attr_not_selected'] = '请选择筛选属性';

?>