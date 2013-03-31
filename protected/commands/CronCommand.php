<?php

class CronCommand extends CConsoleCommand
{
	public function actionScan()
	{
		WotService::scanClan('10633');
	}

	public function actionIndex()
	{
		echo 'hellow!';
	}

}