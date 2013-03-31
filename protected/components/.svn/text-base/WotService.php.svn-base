<?php
class WotService
{
/*
	static private $host='worldoftanks.ru';
	static private $clanUrlJson='/uc/clans/{clanId}/members/?type=table';
	static private $playerUrlJson="http://worldoftanks.ru/community/accounts/{playerId}/";
*/

	//https://gist.github.com/2724734

	static private $wotApiClanUrl="http://worldoftanks.ru/community/clans/{clanId}/api/1.1/?source_token=WG-WoT_Assistant-1.2.2";
	static private $wotApiPlayerUrl="http://worldoftanks.ru/community/accounts/{playerId}/api/1.5/?source_token=WG-WoT_Assistant-1.2.2";


	static private function doRequestJSON($url)
	{
		$host=self::$host;
		$error = 0;
		$data = array();
        $request = "GET $url HTTP/1.0\r\n";
        $request.= "Accept: text/html, */*\r\n";
        $request.= "User-Agent: Mozilla/3.0 (compatible; easyhttp)\r\n";
        $request.= "X-Requested-With: XMLHttpRequest\r\n";
        $request.= "Host: $host\r\n";
        $request.= "Connection: Keep-Alive\r\n";
        $request.= "\r\n";
		$n = 0;
		while(!isset($fp)){
			$fp = fsockopen($host, 80, $errno, $errstr, 15);
			if($n == 3){
				break;
			}
			$n++;
		}
		if (!$fp)
		{
			return "$errstr ($errno)<br>\n";
		} else
		{
			stream_set_timeout($fp,20);
			$info = stream_get_meta_data($fp);
			fwrite($fp, $request);
			$page = '';
			while (!feof($fp) && (!$info['timed_out']))
			{
				$page .= fgets($fp, 4096);
				$info = stream_get_meta_data($fp);
			}
			fclose($fp);
			if ($info['timed_out']) {
				$error = 1; //Connection Timed Out
			}
		}
		if($error == 0){
			preg_match("/{\"request(.*?)success\"}/", $page, $matches);
			$data = (json_decode($matches[0], true));
		}
		else
			return $error;
		return $data;
	}

	static public function updateClanInfo($clan)
	{
		$jsonString=file_get_contents(str_replace('{clanId}', $clan->clan_id, self::$wotApiClanUrl));
		$jsonData=json_decode($jsonString,true);
		if($jsonData['status']=='ok'){
			$clan->clan_descr=$jsonData['data']['description'];
			$clan->updated_at=date('Y-m-d H:i',$jsonData['data']['updated_at']);
			$clan->clan_name=$jsonData['data']['abbreviation'];
			$clan->clan_fullname=$jsonData['data']['name'];
			$clan->clan_descr_html=$jsonData['data']['description_html'];
			$clan->clan_created=date('Y-m-d', $jsonData['data']['created_at']);
			$clan->clan_ico=$jsonData['data']['emblems']['large'];
			$clan->save(false);

			$members=array();
			foreach ($jsonData['data']['members'] as $member) {
				$members[$member['account_id']]=$member;
			}
			
			$tran=Yii::app()->db->beginTransaction();
			
			$clanPlayers=$clan->playersRec;
			foreach ($clanPlayers as $playerId=>$clanPlayerRec){
				if(!isset($members[$playerId]))// Покинул клан
				{
					$clanPlayerRec->escape_date=new CDbExpression('now()');
					$playerClan->save(false);
					continue;
				}
				if($clanPlayerRec->clan_role_id!=$members[$playerId]['role']){
					$clanPlayerRec->clan_role=$players[$playerId]['role'];
					$clanPlayerRec->save(false);
				}
			}
			foreach ($members as $playerId=>$playerData){
				if(!isset($clanPlayers[$playerId])) //Новый член клана
				{
					$player=WotPlayer::model()->findByPk($playerId);
					if(empty($player)){
						$player=new WotPlayer();
						$player->player_id=$playerId;
						$player->player_name=$playerData['account_name'];
						$player->save(false);
					}
					$playerClan=new WotPlayerClan();
					$playerClan->clan_id=$clan->clan_id;
					$playerClan->player_id=$playerId;
					$playerClan->entry_date=date('Y-m-d' ,$playerData['created_at']);
					$playerClan->clan_role=$playerData['role'];
					$playerClan->save(false);
				}
			}
			
			$tran->commit();
		}
		else
			var_dump($jsonData);
	}

	static public function updatePlayerInfo($player)
	{
		$jsonString=file_get_contents(str_replace('{playerId}', $player->player_id, self::$wotApiPlayerUrl));
		$jsonData=json_decode($jsonString,true);
		if($jsonData['status']=='ok'){
			
			$tran=Yii::app()->db->beginTransaction();
			
			foreach ($jsonData['data']['achievements'] as $achievement=>$value){
				$player->$achievement=$value;
			}
			foreach ($jsonData['data']['ratings'] as $rate=>$value){
				$player->$rate=$value['value'];
			}
			foreach ($jsonData['data']['vehicles'] as $vehicle){
				$tank=WotTank::getTank($vehicle['name'],$vehicle['localized_name'],$vehicle['level'],$vehicle['nation'],$vehicle['class'],$vehicle['image_url']);
				unset($vehicle['name']);
				unset($vehicle['localized_name']);
				unset($vehicle['level']);
				unset($vehicle['nation']);
				unset($vehicle['class']);
				unset($vehicle['image_url']);
				$playerTank=WotPlayerTank::getPlayerTank($player->player_id, $tank->tank_id);
				foreach ($vehicle as $param=>$value){
					$playerTank->$param=$value;
				}
				$playerTank->save(false);
			}
			$player->prev_updated_at=$player->updated_at;
			$player->updated_at=date('Y-m-d H:i',$jsonData['data']['updated_at']);
			$player->save(false);
			
			$tran->commit();
		}
		else
			var_dump($jsonData);
	}

	static public function scanClan($clanId)
	{
		$clan=WotClan::model()->findByPk($clanId);
		if(empty($clan)){
			$clan=new WotClan();
			$clan->clan_id=$clanId;
		}
		self::updateClanInfo($clan);
//!!	self::updateClanPlayers($clan);
		$clan->refresh();
		foreach ($clan->players as $player){
			self::updatePlayerInfo($player);
		}

	}

	static public function updateClanPlayers($clan)
	{
		$data=self::doRequestJSON(str_replace('{clanId}', $clan->clan_id, self::$clanUrlJson));
		$items=$data['request_data']['items'];
		$players=array();
		foreach ($items as $playerData) {
			$players[$playerData['account_id']]=$playerData;
		}
		unset($data);
		$clanPlayers=$clan->players;
		foreach ($clanPlayers as $playerId=>$clanPlayer){
			if(!isset($players[$playerId]))// Покинул клан
			{
				$playerClan=WotPlayerClan::model()->findByAttributes(array('player_id'=>$playerId,'clan_id'=>$clan->clan_id,'escape_date'=>null));
				if(!empty($playerClan)){
					$playerClan->escape_date=new CDbExpression('now()');
					$playerClan->save(false);
				}
				continue;
			}
			if($clanPlayer->clan_role!=$players[$playerId]['role']){
				$clanPlayer->clan_role=$players[$playerId]['role'];
				$clanPlayer->save(false);
			}
		}
		foreach ($players as $playerId=>$playerData){
			if(!isset($clanPlayers[$playerId])) //Новый член клана
			{
				$player=WotPlayer::model()->findByPk($playerId);
				if(empty($player)){
					$player=new WotPlayer();
					$player->player_id=$playerId;
					$player->player_name=$playerData['name'];
					$player->save(false);
				}
				$playerClan=new WotPlayerClan();
				$playerClan->clan_id=$clan->clan_id;
				$playerClan->player_id=$playerId;
				$playerClan->entry_date=date('Y-m-d' ,$playerData['member_since']);
				$playerClan->clan_role=$playerData['role'];
				$playerClan->save(false);
			}
		}
	}
}