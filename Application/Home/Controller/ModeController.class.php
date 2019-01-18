<?php
namespace Home\Controller;
use Think\Controller;
class ModeController extends Controller {

	/**
	 * 访问拦截器方法
	 */
    public function _initialize(){

    	if ($this->deploy_path('LOGIN_IMMUNE') == '1')
    	{
    		//跳过登录验证步骤
    	}
    	else
    	{
    		//登录验证
    		if ($_SESSION['login'] != 'true'&&$_SESSION['link'] != 'true') 
    		{
	    		//自动登陆判断  登录名|有效时间Expires|key值 key值 = 加密密码前3+配置salt
	    		$login = explode('|', $_COOKIE["Test_g"]);
	    		$admin_name = $login[0];
	    		$expires = $login[1];
	    		$pwd = substr($login[2],0,3);
	    		$salt = substr($login[2],3);

	    		if (time() > $expires - 3600*24*7&&$salt == C('SALT')) 
	    		{
	    			$Model = M();
	    			$sql ='SELECT p.name,u.id,p.role_all as role,p.power_all as power FROM 
	    			t_user_login as u LEFT JOIN t_user_part as p on p.part_id = r.part_id
	    			WHERE u.name = "'.$admin_name.'" AND u.password LIKE "'.$pwd.'%";
	    			';
	    			$result = $Model->query($sql);
	    			if ($result[0]['id']) 
	    			{
	    				//缓存登录信息
	    				$_SESSION['login'] = 'true';
	    				$_SESSION['name'] = $result[0]['name'];
	    				$_SESSION['id'] = $result[0]['id'];
	    				$_SESSION['role'] = $result[0]['role'];
						$_SESSION['power'] = $result[0]['power'];
						$this->redirect('Index/main');
	    			}
	    		}
	    		else
	    		{
	    			$_SESSION['login'] = 'flase';
	    			$_SESSION['link'] = 'true';
	    			$this->redirect('Index/login');
	    		}
    			
    		}
    		else
    		{
    			//权限过滤
    			$role_new = $this->get_role();
    			if (strpos($_SESSION['role'],$role_new)) 
    			{}
    			else
    			{
    				$this->redirect('Index/exit');
    			}
    		}
    	}
    }
    
    public function get_role()
    {
    	return 0;
    }

    public function main_cp()
    {
        $Model = M();
        $sql = 'SELECT ty.cp_id FROM t_user_type as ty LEFT JOIN t_user_login as log on log.branch_id = ty.id WHERE log.id = "'.$_SESSION['id'].'"';
        $result = $Model->query($sql);
        if ($result[0]['cp_id'] != 'all') 
        {
            return $result[0]['cp_id'];
        }
        else
        {
            return "1";
        }
        
    }
    /**
     * 查询实时路径是否在配置文件中
     */
    public function deploy_path(string $value)
    {
        $immnue = C($value);
        if (array_search($Think.MODULE_NAME,$immnue)||array_search($Think.MODULE_NAME.'/'.$Think.MODULE_NAME,$immnue)||array_search($Think.MODULE_NAME.'/'.$Think.MODULE_NAME.'/'.$Think.ACTION_NAME,$immnue)) 
        {
            return '1';
        }
        return '0';
    }

    /**
     * 查询路径权限码并判断
     */
    public function deploy_power()
    {
        $value = $Think.MODULE_NAME.'/'.$Think.MODULE_NAME.'/'.$Think.ACTION_NAME
        $immnue = C("VALUE_POWER");
        if (strpos($_SESSION['power'],$immnue[$value])) 
        {
            return '1';
        }
        return '0';
    }

    /** 
    * 过滤数据
    * @param string $value 
    * @return string 
    */
    public function filter($value)
    {
        $value = strtr($value, ' ', '');
        $value = addslashes($value);
        return $value;
    }
    public function get_s_menu()
    {
        $Model = M();
        $sql1 = 'SELECT top,id,name,CONCAT(controller,"/",module,"/",method) as url FROM t_menu_role WHERE is_top = "1" AND attest = "1" ;';
        $sql2 = 'SELECT top,id,name,CONCAT(controller,"/",module,"/",method) as url FROM t_menu_role WHERE is_top = "2" AND attest = "1" ;';
        $sql3 = 'SELECT top,id,name,CONCAT(controller,"/",module,"/",method) as url FROM t_menu_role WHERE is_top = "3" AND attest = "1" ;';
        $arr0 = $Model->query($sql1);
        $arr1 = $Model->query($sql2);
        $arr2 = $Model->query($sql3);
        $date = array();
        $link = array();

        foreach ($arr0 as $key => $value) 
        {
            $n = $value['id'];
            $date[$n] = $value;
        }
        foreach ($arr1 as $key => $value) 
        {
            $n = $value['top'];
            $k = $value['id'];
            $date[$n]['list'][$k] = $value;
            $link[$k] = $n;
        }
        foreach ($arr2 as $key => $value) 
        {
            $n = $value['id'];
            $k = $value['top'];
            $j = $link[$k];
            $date[$j]['list'][$k]['list'][$n] = $value;
        }

        return $date;
    }
    /** 
    * 获取菜单
    * @param string $value 
    * @return string 
    */
    public function get_menu($role)
    {
        $Model = M();
        $sql = 'SELECT top,id,name,CONCAT(controller,"/",module,"/",method) as url FROM t_menu_role WHERE is_top = "1" AND attest = "1" ;';
        $menu = array();
        $result = $Model->query($sql);
        if ($role != "all") 
        {
            foreach ($result as $key => $value) 
            {
                if (strpos($role,$value['id'])) 
                {
                    $list[$i] = $result[$key];
                }
            }
        }
        else
        {
            $list = $result;
        }

        //读取最顶层菜单
        $menu = $this->get_menu_list($list,$role);
        return $menu;
    }

    /** 
    * 获取下层菜单(迭代最差解)
    * @param string $value 
    * @return string 
    */
    public function get_menu_list($menu,$role)
    {
    	foreach ($menu as $key => $value) 
    	{
	    	$sql = 'SELECT top,id,name,CONCAT(controller,"/",module,"/",method) as url FROM t_menu_role WHERE is_top = "0" AND attest = "1" AND top = "'.$value['id'].'" ;';
	    	$result = $Model->query($sql);

            if ($role != "all") 
            {
                foreach ($result as $key => $value) 
                {
                    if (strpos($role,$value['id'])) 
                    {
                        $list[$i] = $result[$key];
                    }
                }
            }
            else
            {
                $list = $result;
            }

	    	if ($list[0]['down'] == 1) 
	    	{
	    		$menu[$key]['list'] = get_menu_list($list,$role);
	    	}
	    	else
	    	{
				$menu[$key]['list'] = $list;
	    	}	
    	}
    	return $menu;
    }

}