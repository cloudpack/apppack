<?php
class Utils
{
	public static function to_job_name($val)
	{
		switch ($val) {
		case 1:
			return "未就業";
			break;
		case 2:
			return "就業中";
			break;
		case 3:
			return "学生";
			break;
		default:
			return "";
		}
	}
}
