<?php

class WotReport
{

	private static $activitySql=<<<SQL
select pt.battle_count-pth.battle_count b, pt.* from wot_player_tank pt
  join
(select a.player_id,a.tank_id,
  (select min(pth.updated_at) from wot_player_tank_history pth where pth.updated_at > date_add(a.last_updated_at,interval -1 day) and pth.player_id=a.player_id and pth.tank_id=a.tank_id) last_updated_at
        from
 (SELECT pth.player_id
     , pth.tank_id
     , max(pth.updated_at) last_updated_at
FROM
  wot_player_tank_history pth
GROUP BY
  pth.player_id
, pth.tank_id
  ) a 
  ) a on a.player_id=pt.player_id and a.tank_id=pt.tank_id
  join wot_player_tank_history pth on pth.player_id=a.player_id and pth.tank_id=a.tank_id and pth.updated_at=a.last_updated_at
 -- where  pt.battle_count=pth.battle_count
  order by pt.player_id, pt.battle_count-pth.battle_count
DESC	
SQL;
	
	public static function report($reportName)
	{
		$data=Yii::app()->db->cache(3600)->createCommand(self::$activitySql)->queryAll();
		return json_encode($data);
	}


}