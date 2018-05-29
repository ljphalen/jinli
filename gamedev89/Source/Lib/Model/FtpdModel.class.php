<?php
class FtpdModel extends Model
{
	protected $connection = "FTPD_DB_CONFIG";
	protected $trueTableName = "users";
	
	function addUser($uid)
	{
		$user = $this->rand($uid);
		$user["Uid"] = C("FTPD_UID");
		$user["Gid"] = C("FTPD_GID");
		$user["Dir"] = rtrim(C("FTPD_PATH"), "/")."/".$uid;
		$user["Created_at"] = time();

		@mkdirs($user["Dir"]);
		
		$handle = $this->add($user);
		if(false === $handle)
		{
			Log::write("FTP ADD USER ERROR:".$this->_sql(), Log::EMERG);
			return false;
		}
		
		return $user;
	}
	
	function getUser($uid)
	{
		if(empty($uid))
			return false;
		
		$find = $this->where(array("ID"=>$uid, "User"=>"user_".$uid))->find();
		return $find;
	}
	
	function reset($uid)
	{
		$user = $this->rand($uid);
		$this->data($user)->where(array("ID"=>$uid))->save();
		
		return $this->getUser($uid);
	}
	
	function rand($uid)
	{
		$rand = md5(rand() . microtime(true) . uniqid($uid));
		return array("ID"=>$uid, "User"=>"user_" . $uid, "Password"=>substr($rand, 12, 12));
	}
	
	function AccountCleanUp($day)
	{
		$day = intval($day) ? intval($day) : 3;
		$time = strtotime("-{$day} days");
		return $this->where("Created_at <= {$time}")->delete();
	}

}