<?php
use Aws\S3\S3Client;

class Model_Sample extends Model
{

  public static function upper($word)
	{
		return Str::upper($word);
	}

  public static function find()
	{
		$s3 = S3Client::factory(\Config::get('aws.credential'));
    $result = $s3->listBuckets();
    $list = array();
    foreach ($result['Buckets'] as $bucket) {
      $list[] = $bucket;
    }
		return $list;
	}

}
