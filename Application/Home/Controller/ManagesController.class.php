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

    public function ajax_add_submit()
    {
      //取消增加接口  
    }

    /**
     * 重构菜单修改页面
     */  
    public function ajax_edit_submit()
    {
        if ($_POST) 
        {
            $tbName = $this->get_tbname();
            $tbFields = $this->_edittbFields();
            $val = $this->edit_date($_POST['date']);
            $field = '';
            //提取过滤字段
            for ($i=0; $i < count($val); $i++) 
            { 
                $field = $field.",( ";
                $j=1;
                foreach ($tbFields as $key => $value) 
                {

                    if ($j == 1) 
                    {
                        $field = $field."'".$this->filter($val[$i]['id'])."'";
                        $j++;
                    }
                    if ($val[$i][$key]!=''&&$key != 'id') 
                    {
                        $field = $field.",'".$this->filter($val[$i][$key])."'";
                    }
                    if ($i==0) {
                        $fid = $fid.','.$key;
                    }
                    
                }
                $field = $field." ) ";
            }
            $field = substr($field,1);

            $Model = M();
            $ids = array_column($val, 'id');
            $sql = 'delect * FROM'.$tbName.' WHERE id IN('.implode(',',$ids);.') ;';
            if ($Model->query($sql)) 
            {
                $sql = 'INSERT INTO '.$tbName.' 
                        (id,'.$fid.')
                            VALUES 
                        '.$field.';';
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