<?php
namespace Home\Controller;
use Home\Controller\ModeController;
class AjaxController extends ModeController 
{

    /**
     * 增加页面
     */
    public function add()
    {
        $this->display();
    }

    /**
     * 修改页面
     */
    public function edit()
    {
        $this->display();
    }

    /**
     * 重写访问拦截器方法
     */
    public function _initialize()
    {
        //权限过滤
        if ($this->deploy_path('LOGIN_IMMUNE')) 
        {

        }
        else
        {
            $this->redirect('Index/exit');
        }
        
    }
    /**
     * 主页
     */
    public function index()
    {
        $this->display();
    }

    /**
     * 数据添加
     */
    public function ajax_add_submit()
    {
        if ($_POST) 
        {
            $tbName = $this->get_tbname();
            $tbFields = $this->_tbFields();
            $val = $this->add_date($_POST);
            //提取过滤字段
            foreach ($tbFields as $key => $value) 
            {
                if ($val[$key]!=''&&$key != 'id') 
                {
                    $field = $field.",'".$this->filter($val[$key])."'";
                }
                $fid = $fid.','.$key;
            }
            $Model = M();
            $id = $val['id'];
            $sql = 'INSERT INTO '.$tbName.' 
                    (id,'.$fid.')
                        VALUES
                    ("'.$id.'",'.$field.');';
            $result = $Model->query($sql);
            if ($result) 
            {
                $rester['code'] = 0;
                $rester['reason'] = 'defeat';
                $rester['output_type'] = 'json';
                $rester['text'] = '成功';
            }
            else
            {
                $rester['code'] = 1;
                $rester['reason'] = 'defeat';
                $rester['output_type'] = 'json';
                $rester['text'] = '失败';
            }
        }
        return json_encode($rester); 
    }

    /**
     * 数据展示
     */
    public function ajax_submit()
    {
        $tbName = $this->get_tbname();
        $date = $this->ajax_post();
        
        $sql = $this->ajax_sql();
        if (is_array($sql)) 
        {
            $result = $sql;
        }
        else
        {
            $sql = $sql.' ORDER BY '.$date['order'].' 
                 LIMIT '.$date['begin'].','.$date['limit'].';';
            $result = $Model->query($sql);
        }

        if ($result[0]) 
        {
            $rester['code'] = 0;
            $rester['reason'] = 'defeat';
            $rester['output_type'] = 'json';
            $rester['text'] = '成功';
            $rester['date'] = $result;
        }else
        {
            $rester['code'] = 1;
            $rester['reason'] = 'defeat';
            $rester['output_type'] = 'json';
            $rester['text'] = '失败';
        }
            
        return json_encode($rester);
    }

    /**
     * 获取post信息
     */
    public function ajax_post()
    {
        $arr =  array('0' => 'order','1' => 'limit','2' => 'begin', );
        $variable = $_POST;
        $_POST = null;
        foreach ($variable as $key => $value) 
        {
            if (array_search($key,$arr)) 
            {
                $re[$key] = $this->filter($value);
            }
        }
        return $re;
    }

    /**
     * 数据修改
     */
    public function ajax_edit_submit()
    {
        if ($_POST) 
        {
            $tbName = $this->get_tbname();
            $tbFields = $this->_edittbFields();
            $val = $this->edit_date($_POST);
            //提取过滤字段
            foreach ($tbFields as $key => $value) 
            {
                if ($val[$key]!=''&&$key != 'id') 
                {
                    $field = $field.",'".$this->filter($val[$key])."'";
                }
                $fid = $fid.','.$key;
            }
            $Model = M();
            $sql = 'delect * FROM'.$tbName.' WHERE id = "'.$val['id'].'";';
            if ($Model->query($sql)) 
            {
                $sql = 'INSERT INTO '.$tbName.' 
                        (id,'.$fid.')
                            VALUES
                        ("'.$val['id'].'",'.$field.');';
                $result = $Model->query($sql);
                if ($result) 
                {
                    $rester['code']=0;
                    $rester['reason']='defeat';
                    $rester['output_type']='json';
                    $rester['text']='成功';
                }
                else
                {
                    $rester['code']=1;
                    $rester['reason']='defeat';
                    $rester['output_type']='json';
                    $rester['text']='修改失败，写入日志';
                }
            }
            else
            {
                $rester['code']=1;
                $rester['reason']='defeat';
                $rester['output_type']='json';
                $rester['text']='修改失败，写入日志';
            }

        }
        return json_encode($rester);
    }

    /**
     * 获取查询sql
     */
    public function ajax_sql()
    {
        $ret = '';
        return $ret;
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
     * 修改_数据添加
     */
    public function edit_date($date)
    {
        $date = $date;
        return $date;
    }

    /**
     * 获取表名
     */
    public function get_tbname()
    {
        $ret = '';
        return $ret;
    }

    /**
     * 增加_字段添加
     */
    public function add_fieds($ret)
    {
        return $ret;
    }

    /**
     * 修改_字段添加
     */
    public function edit_fieds($ret)
    {
        return $ret;
    }

    /** 
    * 取得数据表的字段信息 
    * @param string $tbName 表名
    * @return array 
    */
    protected function _tbFields($tbName) 
    {
        $Model = M();
        $sql = 'SELECT COLUMN_NAME AS title  FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME="'.$tbName.'" AND TABLE_SCHEMA="test";';
        $stmt = $Model->query($sql);
        foreach ($stmt as $key=>$value) 
        {
            $ret[$value['title']] = 1;
        }
        $ret = $this->add_fieds($ret);
        return $ret;
    }

    /** 
    * 取得数据表的字段信息 
    * @param string $tbName 表名
    * @return array 
    */
    protected function _edittbFields($tbName) 
    {
        $Model = M();
        $sql = 'SELECT COLUMN_NAME AS title  FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME="'.$tbName.'" AND TABLE_SCHEMA="test";';
        $stmt = $Model->query($sql);
        foreach ($stmt as $key=>$value) 
        {
            $ret[$value['COLUMN_NAME']] = 1;
        }
        $ret = $this->edit_fieds($ret);
        return $ret;
    }    
}