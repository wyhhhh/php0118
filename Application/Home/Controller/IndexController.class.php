<?php
namespace Home\Controller;
use Home\Controller\ModeController;

class IndexController extends ModeController {

	/**
	 * 起始页
	 */
    public function index()
    {

    }

	/**
	 * 当前权限码
	 */
    public function get_role()
    {
    	return '0';
    }

	/**
	 * 主页
	 */
    public function main()
    {
    	$role = $_SESSION['role'];
    	$menu = $this->get_menu($role);
    	$this->assign('menu',$menu);
    }

	/**
	 * 登录页
	 */
    public function login()
    {
		if (isset($_POST)) 
		{

			$admin = $this->filter($_POST['admin']);
			$pwd = $_POST['pwd'];
			if (strlen($admin) > 3&&strlen($admin) < 10&&$pwd != '') 
			{
				$redis = new \Redis();
				$redis->connect('127.0.0.1',6379);

				$session_id=session_id();
				$num = $redis->get($session_id."err_login")
				if ($num < 3)
				{
					$Model = M();
					$sql = 'SELECT u.id,u.password,u.attest,p.role_all,p.power_all,p.name FROM
					t_user_login as u LEFT JOIN t_user_part as p on p.id = r.id
					WHERE u.name = "'.$admin.'";';
					$pwd = crypt($pwd,"starcor");
					$result = $Model->query($sql);
					if ($result[0]['attest'] == 1) {
						if ($result[0]['password'] == crypt($pwd,$admin)) 
						{
		    				$_SESSION['login'] = 'true';
		    				$_SESSION['name'] = $result[0]['name'];
		    				$_SESSION['id'] = $result[0]['id'];
		    				$_SESSION['role'] = $result[0]['role'];
							$_SESSION['power'] = $result[0]['power'];

							$pwd = substr($result[0]['password'],0,3);
							$time = time() + 3600*24*7;
							$value = $admin."|".$time."|".$pwd.C('SALT');
							setcookie("Test_g",$value,$time);

					        $rester['code']=0;
					        $rester['reason']='defeat';
					        $rester['output_type']='json';
							return json_encode($rester);
						}
						else
						{
					        $rester['code']=1;
					        $rester['reason']='defeat';
					        $rester['output_type']='json';
					        $rester['text']='密码错误';
						}
					}
					else
					{
				        $rester['code']=1;
				        $rester['reason']='defeat';
				        $rester['output_type']='json';
				        $rester['text']='账号错误或被禁止登录';
					}

					$redis->set($session_id."err_login",$num+1);
					$redis->expire($session_id."err_login",180);
				}
				else
				{
			        $rester['code']=1;
			        $rester['reason']='defeat';
			        $rester['output_type']='json';
			        $rester['text']='账户封存,请三分钟后再尝试';
				}
			}
			else
			{
		        $rester['code']=1;
		        $rester['reason']='defeat';
		        $rester['output_type']='json';
		        $rester['text']='数据错误';
			}
			return json_encode($rester);
		}
		else
		{
			$this->display("");
		}
    }

	/**
	 * 禁止
	 */
    public function exit()
    {
    	echo "当前禁止访问";
    }
}