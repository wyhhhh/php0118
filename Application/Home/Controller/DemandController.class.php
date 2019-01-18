<?php
namespace Home\Controller;
use Home\Controller\AjaxController;

class DemandController extends AjaxController {

    /**
     * 查询功能基础代码
     */  
    public function ajax_sql()
    {
        $sql = 'SELECT d.name,d.id,d.note,cp.name as cname FROM t_demand as d LEFT JOIN t_cp as cp on cp.id = d.cp_id ';
        $cp = $this->main_cp();
        $where = '';
        if ($cp != '1') 
        {
            $where = ' WHERE d.cp_id = "'.$cp.'"' ;
        }
        return $sql.$where;
    }

    /**
     * 表名预设值
     */  
    public function get_tbname()
    {
        $table = 't_demand';
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