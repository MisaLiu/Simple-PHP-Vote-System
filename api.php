<?php
    define('IN_STATION', true);

    require_once('config.php');
    require_once('MysqliDb.php');

    $return = array();

    $db = new MysqliDb(Array(
        'host'     => $_CONFIG['db']['host'],
        'port'     => $_CONFIG['db']['port'],
        'username' => $_CONFIG['db']['username'],
        'password' => $_CONFIG['db']['password'],
        'db'       => $_CONFIG['db']['name'],
        'prefix'   => $_CONFIG['db']['prefix'],
        'charset'  => $_CONFIG['db']['charset']
    ));

    $DATA = $_POST; // 你可以自己在这里将传参方式改成 $_GET 或者 $_POST，注意同时修改 JavaScript 中的请求方式

    if (empty($DATA)) {
        $return['code'] = -100;
        $return['msg']  = '请使用正确的方式请求！';

        die(json_encode($return));
    }  

    // 获取/初始化用户信息
    if ($DATA['mod'] == 'getUserId') {
        if (!empty($_COOKIE['userid'])) {
            $db->where('userid', $_COOKIE['userid']);
            $data = $db->getOne('users');
            
            if (!empty($data)) {
                $data['logIp'] = getRealIp();

                $db->where('userid', $data['userid']);
                if (!$db->update('users', $data)) {
                    $return['code'] = -30;
                    $return['msg'] = '读取用户信息失败！';

                    die (json_encode($return));
                }

                if (!empty($data['votes'])) 
                    $data['votes'] = explode(',', $data['votes']);
                else
                    $data['votes'] = array();

                $return['code'] = 200;
                $return['data'] = $data;

                die (json_encode($return));
            }
        }

        $db->where('regIp', getRealIp());
        $db->where('logIp', getRealIp(), '=', 'OR');

        $data = $db->getOne('users');

        if (!empty($data)) {
            $data['logIp'] = getRealIp();

            $db->where('userid', $data['userid']);
            if (!$db->update('users', $data)) {
                $return['code'] = -30;
                $return['msg'] = '读取用户信息失败！';

                die (json_encode($return));
            }

            if (!empty($data['votes'])) 
                $data['votes'] = explode(',', $data['votes']);
            else
                $data['votes'] = array();

            $return['code'] = 200;
            $return['data'] = $data;

            die (json_encode($return));

        } else {
            $data = array(
                'userid' => guid(),
                'regIp'  => getRealIp(),
                'logIp'  => getRealIp(),
                'votes'  => ''
            );

            $id = $db->insert('users', $data);

            if ($id) {
                $data['votes'] = array();

                $return['code'] = 200;
                $return['data'] = $data;

                die (json_encode($return));

            } else {
                $return['code'] = -30;
                $return['msg'] = '读取用户信息失败！';

                die (json_encode($return));
            }
        }
    }

    // 获取投票项目列表
    if ($DATA['mod'] == 'getItemList') {
        $data = $db->get('items');

        if (count($data) <= 0) {
            $return['code'] = -50;
            $return['msg'] = '还没有任何可投票的项目哦！';

            die(json_encode($return));
        }

        $return['code'] = 200;
        $return['data'] = $data;

        die(json_encode($return));
    }

    // 给项目投票
    if ($DATA['mod'] == 'voteItem') {
        if (empty($_COOKIE['userid'])) {
            $return['code'] = -40;
            $return['msg'] = '用户 ID 不能为空！';

            die (json_encode($return));
        }

        if (empty($DATA['id'])) {
            $result['code'] = -80;
            $result['msg'] = '投票的 ID 不能为空！';

            die(json_encode($result));
        }

        if ($DATA['id'] <= 0) {
            $result['code'] = -70;
            $result['msg'] = '投票的 ID 不合法！';

            die(json_encode($result));
        }

        // 取项目信息
        $db->where('id', $DATA['id']);
        $itemInfo = $db->getOne('items');

        if (!$itemInfo) {
            $result['code'] = -60;
            $result['msg'] = '不存在该项目！';

            die(json_encode($result));
        }
        
        // 取用户信息
        $db->where('userid', $_COOKIE['userid']);
        $userInfo = $db->getOne('users');
        
        if (!$userInfo) {
            $result['code'] = -20;
            $result['msg'] = '不存在该用户！';

            die(json_encode($result));
        }
        
        if (!empty($userInfo['votes']))
            $userInfo['votes'] = explode(',', $userInfo['votes']);
        else
            $userInfo['votes'] = array();
        
        if (count($userInfo['votes']) >= $_CONFIG['vote']['votesPerPerson']) {
            $result['code'] = -110;
            $result['msg'] = '你已经投完了所有的票，不能再投了！';

            die(json_encode($result));
        }

        for ($i = 0; $i < count($userInfo['votes']); $i++) {
            if ($userInfo['votes'][$i] == $itemInfo['id']) {
                $result['code'] = -10;
                $result['msg'] = '你已经给这个项目投过票了！';

                die(json_encode($result));
            }
        }

        $itemInfo['votes'] += 1;
        $userInfo['votes'][] = $itemInfo['id'];
        $userInfo['votes'] = implode(',', $userInfo['votes']);

        $db->where('id', $itemInfo['id']);
        if (!$db->update('items', $itemInfo)) {
            $result['code'] = -90;
            $result['msg'] = '写入信息时失败！';

            die(json_encode($result));
        }

        $db->where('userid', $userInfo['userid']);
        if (!$db->update('users', $userInfo)) {
            $result['code'] = -90;
            $result['msg'] = '写入信息时失败！';

            die(json_encode($result));
        }

        $result['code'] = 200;
        $result['msg'] = '投票成功';
        $result['data'] = $itemInfo;

        die(json_encode($result));
    }

    // 给项目取消投票
    if ($DATA['mod'] == 'unvoteItem') {
        if (empty($_COOKIE['userid'])) {
            $return['code'] = -40;
            $return['msg'] = '用户 ID 不能为空！';

            die (json_encode($return));
        }

        if (empty($DATA['id'])) {
            $result['code'] = -80;
            $result['msg'] = '投票的 ID 不能为空！';

            die(json_encode($result));
        }

        if ($DATA['id'] <= 0) {
            $result['code'] = -70;
            $result['msg'] = '投票的 ID 不合法！';

            die(json_encode($result));
        }

        // 取项目信息
        $db->where('id', $DATA['id']);
        $itemInfo = $db->getOne('items');

        if (!$itemInfo) {
            $result['code'] = -60;
            $result['msg'] = '不存在该项目！';

            die(json_encode($result));
        }
        
        // 取用户信息
        $db->where('userid', $_COOKIE['userid']);
        $userInfo = $db->getOne('users');
        
        if (!$userInfo) {
            $result['code'] = -20;
            $result['msg'] = '不存在该用户！';

            die(json_encode($result));
        }
        
        if (!empty($userInfo['votes']))
            $userInfo['votes'] = explode(',', $userInfo['votes']);
        else
            $userInfo['votes'] = array();

        if (count($userInfo['votes']) <= 0) {
            $result['code'] = -110;
            $result['msg'] = '你还没有进行投票，无法取消投票！';

            die(json_encode($result));
        }

        for ($i = 0; $i < count($userInfo['votes']); $i++) {
            if ($userInfo['votes'][$i] == $itemInfo['id']) {
                break;

            } elseif ($userInfo['votes'][$i] != $itemInfo['id'] && ($i + 1) == count($userInfo['votes'])) {
                $result['code'] = -10;
                $result['msg'] = '你没有给这个项目投过票！';

                die(json_encode($result));
            }
        }

        $itemInfo['votes'] -= 1;
        $userInfo['votes'] = array_diff($userInfo['votes'], array($itemInfo['id']));
        $userInfo['votes'] = implode(',', $userInfo['votes']);

        $db->where('id', $itemInfo['id']);
        if (!$db->update('items', $itemInfo)) {
            $result['code'] = -90;
            $result['msg'] = '写入信息时失败！';

            die(json_encode($result));
        }

        $db->where('userid', $userInfo['userid']);
        if (!$db->update('users', $userInfo)) {
            $result['code'] = -90;
            $result['msg'] = '写入信息时失败！';

            die(json_encode($result));
        }

        $result['code'] = 200;
        $result['msg'] = '取消投票成功';
        $result['data'] = $itemInfo;

        die(json_encode($result));
    }


    // =====函数声明区=====
    // 获取用户真实 IP
    function getRealIp() {
		$ip=false;
			if(!empty($_SERVER["HTTP_CLIENT_IP"])){
        	$ip = $_SERVER["HTTP_CLIENT_IP"];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        	$ips = explode (", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
        	if ($ip) { 
				array_unshift($ips, $ip); $ip = FALSE; 
			}
            for ($i = 0; $i < count($ips); $i++) {
            	if (!mb_eregi ("^(10│172.16│192.168).", $ips[$i])) {
                	$ip = $ips[$i];
                    break;
                }
            }
        }
    	return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
	}

    // 生成 UUID
    function guid() {
        if (function_exists('com_create_guid')) {
            return com_create_guid();
        } else {
            mt_srand((double)microtime() * 10000);
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);
            $uuid = substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid,12, 4).$hyphen
                .substr($charid,16, 4).$hyphen
                .substr($charid,20,12);
            return $uuid;
        }
    }
?>