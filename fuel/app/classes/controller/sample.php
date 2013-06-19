<?php

use Fluent\Logger\FluentLogger;

class Controller_Sample extends Controller{

	public function action_upper($word=null)
	{
		Log::warning("word=".$word);

		$data = array();
		$data['word'] = $word;
		$data['converted'] = Model_Sample::upper($word);
		
		//file_log(fuel/app/logs/yyyy/mm/dd)
		Log::error("response=".print_r($data, true));

		//fluent_log(unix domain socket)
		$logger = new FluentLogger("unix:///var/run/td-agent/td-agent.sock");
		$logger->post('app.fuel', $data);

		Log::error(print_r(Model_Blog::find_by_pk(1), true));

		return Response::forge(View::forge('sample/index', $data));
	}

}
