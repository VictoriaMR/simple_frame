<?php 

namespace app\Services;

use App\Services\Base as BaseService;
use App\Models\Member;
use frame\Session;

/**
 * 	用户公共类
 */
class MemberService extends BaseService
{	
	public function __construct()
    {
        $this->baseModel = new Member();
    }

	/**
	 * @method 创建用户
	 * @author Victoria
	 * @date   2020-01-12
	 * @return boolean
	 */
	public function create($data)
	{
		if (empty($data['password'])) return false;
		$data['salt'] = $this->getSalt();
		$data['password'] = \Hash::make($this->getPasswd($data['password'], $data['salt']));

		return $this->baseModel->create($data);
	}
	/**
	 * @method 获取随机数
	 * @author Victoria
	 * @date   2020-01-10
	 * @return string salt
	 */
	public function getSalt($len = 4)
	{
		$chars = array(
			'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k',
			'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v',
			'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G',
			'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R',
			'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '0', '1', '2',
			'3', '4', '5', '6', '7', '8', '9'
		);
 
		$charsLen = count($chars) - 1;
		shuffle($chars);                            //打乱数组顺序
		$str = '';
		for($i=0; $i<$len; $i++){
		 	$str .= $chars[mt_rand(0, $charsLen)];    //随机取出一位
		}
		return $str;
	}

	/**
	 * @method 获取密码与随机值的组合
	 * @author Victoria
	 * @date   2020-01-10
	 * @return string password
	 */
	public function getPasswd($password, $salt)
	{
		$passwordArr = str_split($password);
		$saltArr = str_split($salt);
		$countpwd = count($passwordArr);
		$countSalt = count($saltArr);

		$password = '';
		if ($countSalt > $countpwd) {
			foreach ($saltArr as $key => $value) {
				$password .= $passwordArr[$key] ?? '' . $value;
			}
		} else {
			$i = 0;
			$sign = floor($countpwd / $countSalt);
			foreach ($passwordArr as $key => $value) {
				$password .= $value;
				if ($key % $sign == 0) {
					if (empty($saltArr[$i])) $i = 0;

					$password .= $saltArr[$i];
					$i ++;
				}
			}
		}

		return $password;
	}

	/**
	 * @method 登陆
	 * @author Victoria
	 * @date   2020-01-11
	 * @param  string     $name    	名称或者手机号码
	 * @param  string     $password 密码
	 * @return array
	 */
	public function login($mobile, $password, $isToken = false, $isAdmin = false)
	{
		if (empty($mobile) || empty($password)) return false;

		$info = $this->getInfoByMobile($mobile);

		if (empty($info)) return false;

		if (!$info['status']) return false;

		$password = $this->getPasswd($password, $info['salt']);

		if ($this->checkPassword($password, $info['password'])) {
			$data = [
				'mem_id' => $info['mem_id'],
				'name' => $info['name'],
				'mobile' => $info['mobile'],
				'nickname' => $info['nickname'],
			];

			if ($isAdmin) {
				if (!empty($info['color_id'])) {
					$colorService = \App::make('App\Services\ColorService');
					$colorInfo = $colorService->loadData($info['color_id']);
				}
				$data['is_super'] = $info['is_super'];
				$data['color_name'] = $colorInfo['color_name'] ?? '';
				$data['color_value'] = $colorInfo['color_value'] ?? '';
			}

			return Session::set($isAdmin ? 'admin' : 'home', $data);
		}

		return false;
	}

	/**
	 * @method 检查两者密码
	 * @author Victoria
	 * @date   2020-03-22
	 * @param  string        $inPassword     输入密码
	 * @param  string        $sourcePassword 源密码
	 * @return boolean
	 */
	public function checkPassword($inPassword = '', $sourcePassword = '')
	{
		return password_verify($inPassword, $sourcePassword);
	}

	/**
	 * @method 通过用户ID生成token
	 * @author Victoria
	 * @date   2020-01-11
	 * @param  integer       $userId   
	 * @param  integer       $loginType
	 * @return array token
	 */
    public function generateToken($userId, $loginType=0)
    {
        $token = $refreshToken = null;
        $maxTrayCount = 10;
        
        $counter = 0;
        do {
            $token = Str::random(32);
        } while (Cache::get($token) && ($counter++) < $maxTrayCount);

        $counter = 0;
        do {
            $refreshToken = Str::random(32);
        } while (Cache::get($refreshToken) && ($counter++) < $maxTrayCount);

        if (empty($token) || empty($refreshToken)) return [];
        
        $expiresIn = self::constant('TOKEN_EXPIRED'); //6小时
        $refreshExpiresIn = self::constant('REFRESH_TOKEN_EXPIRED'); //15天
               
        Cache::put($token, join(':', [$userId, $loginType, $expiresIn, $refreshToken, 0, \Carbon\Carbon::now()]), $expiresIn);
        Cache::put($refreshToken, join(':', [$userId, $loginType, $refreshExpiresIn, $token, 1, \Carbon\Carbon::now()]), $refreshExpiresIn);

        // 记录用户token记录
        $this->recordToken(
            $userId,
            [$token, $expiresIn],
            [$refreshToken, $refreshExpiresIn]
        );
        
        return [
            'access_token' => $token,
            'refresh_token' => $refreshToken,
            'expires_in' => $expiresIn * 60, // 换成秒
        ];
    }

    /**
     * 记录token记录
     * @author Victoria
	 * @date   2020-01-11
	 * @param  integer       $userId   
	 * @return array token
	 */
    protected function recordToken($userId, $tokenInfo, $refreshTokenInfo)
    {
        if (empty($userId)) return ;
        
        $tokenKey = 'token:' . $userId;
        $refreshTokenKey = 'refreshToken:' . $userId;

        $tokenList = Cache::get($tokenKey);
        $refreshTokenList = Cache::get($refreshTokenKey);

        if (empty($tokenList)) $tokenList = [];
        if (empty($refreshTokenList)) $refreshTokenList = [];

        list($token, $tokenExpiresIn) = $tokenInfo;
        list($refreshToken, $refreshTokenExpiresIn) = $refreshTokenInfo;
        
        $tokenList[] = $token;
        $refreshTokenList[] = $refreshToken;

        Cache::put($tokenKey, $tokenList, $tokenExpiresIn);
        Cache::put($refreshTokenKey, $refreshTokenList, $refreshTokenExpiresIn);

        return true;
    }

	/**
	 * @method 根据名称或者手机号码获取信息
	 * @author Victoria
	 * @date   2020-01-11
	 * @return array
	 */
	public function getInfoByName($name)
	{
		if (empty($name)) return [];

		return $this->baseModel->getInfoByName($name);
	}

	/**
	 * @method 根据手机号码获取信息
	 * @author Victoria
	 * @date   2020-01-11
	 * @return array
	 */
	public function getInfoByMobile($name)
	{
		if (empty($name)) return [];

		return $this->baseModel->getInfoByMobile($name);
	}

	/**
	 * @method 检查用户存在
	 * @author Victoria
	 * @date   2020-01-11
	 * @return boolean
	 */
	public function isExistUserByName($name) 
	{
		return $this->baseModel->isExistUserByName($name);
	}

	/**
	 * @method 检查用户存在
	 * @author Victoria
	 * @date   2020-01-11
	 * @return boolean
	 */
	public function isExistUserByMobile($name) 
	{
		return $this->baseModel->isExistUserByMobile($name);
	}

	/**
     * 通过ID获取用户信息
     * 
     * @param string $userId
     * @return array 用户信息
     */
    public function getInfo($userId)
    {
        return $this->baseModel->getInfo($userId);
    }

    /**
     * @method 获取缓存数据
     * @author Victoria
     * @date   2020-01-12
     * @param  integer      $userId 
     * @return array
     */
    public function getInfoCache($userId)
    {
        $cacheKey = $this->getInfoCacheKey($userId);

        if ($info = Cache::get($cacheKey)) {
            return $info;
        } else {
            $info = $this->getInfo($userId);

            Cache::put($cacheKey, $info, self::constant('INFO_CACHE_TIMEOUT'));

            return $info;
        }
    }

    /**
     * @method 缓存key
     * @author Victoria
     * @date   2020-01-12
     * @param  string      $userId 
     * @return string cachekey
     */
    public function getInfoCacheKey($userId)
    {
        return 'MEMBER_INFO_CACHE_' . $userId;
    }

    /**
     * @method 清除缓存
     * @author Victoria
     * @date   2020-01-12
     * @param  integer      $userId 
     * @return boolean
     */
    public function clearCacheKey($userId)
    {
        $cacheKey = $this->getInfoCacheKey($userId);
        $info = $this->getInfo($userId);
        return Cache::foget($cacheKey);
    }

    /**
     * @method 获取用户列表
     * @author Victoria
     * @date   2020-01-12
     * @return array list
     */
    public function getList($data, $page = 1, $pagesize = 20)
    {
    	$filter = [];

    	$total = $this->getDataCount($data);

    	if ($total > 0) {
    		$field = [
    			'mem_id', 
    			'nickname', 
    			'name',
    			'mobile',
    			'email',
    			'is_super',
    			'status',
    			'create_at',
    			'login_at',
    		];
    		$list = $this->getDataList($data, $field, ['page' => $page, 'pagesize' => $pagesize], [['create_at', 'DESC']]);

    		foreach ($list as $key => $value) {
    			$value['create_at'] = date('Y-m-d', $value['create_at']);
    			$list[$key] = $value;
    		}
    	}

    	return $this->getPaginationList($list ?? [], $total, $page, $pagesize);
    }
}