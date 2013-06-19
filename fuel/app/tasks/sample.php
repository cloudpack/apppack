<?php

namespace Fuel\Tasks;

use Aws\S3\S3Client;


class Sample 
{

	public static function run()
	{
		$s3 = S3Client::factory(\Config::get('aws.credential'));
		$result = $s3->listBuckets();
		foreach ($result['Buckets'] as $bucket) {
			\Cli::write(print_r($bucket, true)); 
		}
	}

  public static function hoge($word=null)
	{
		\Cli::write($word);
	}

}

/* End of file tasks/robots.php */
