<?php
namespace Home\Controller;
use Home\Controller\AjaxController;

class ManagesController extends AjaxController {

    /**
     * 管理类别-查询功能基础代码
     */  
    public function ajax_sql()
    {
        return $this->get_menu("all");
    }

    /**
     * 表名预设值
     */  
    public function get_tbname()
    {
        $table = 't_user_type';
        return $table;
    }

    /**
     * 增加_数据添加
     */
    public function add_date($date)
    {
        $date = $date;
        return $date;
    }

    /**
     * 增加_字段添加
     */
    public function add_fieds($ret)
    {
        return $ret;
    }

    /**
     * 修改_数据添加
     */
    public function edit_date($date)
    {
        $date = $date;
        return $date;
    }

    /**
     * 修改_字段添加
     */
    public function edit_fieds($ret)
    {
        return $ret;
    }

}