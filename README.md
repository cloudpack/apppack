# apppack 

このプロジェクトではfuelphpにaws-sdk-phpとphpunitを組み込んだものをベースとしています。  
使用しているバージョンは以下の通りです。

* fuelphp 1.6
* aws-sdk-php 2.0
* phpunit 3.7
* fluent-logger-php v0.3.7

---
## 参考資料
　  
[fuelphpドキュメント1.6 (英語)](http://fuelphp.com/docs/index.html)  
[fuelphpドキュメント1.3 (日本語)](http://press.nekoget.com/fuelphp_doc/index.html)  
[fuelphp (github)](https://github.com/fuelphp/)  
[aws-sdk-phpドキュメント2.0 (英語)](http://docs.aws.amazon.com/aws-sdk-php-2/latest/)  
[aws-sdk-php 2.0 (github)](https://github.com/aws/aws-sdk-php/)  
[phpunitドキュメント3.7 (日本語)](http://phpunit.de/manual/3.7/ja/index.html)  
[phpunit (github)](https://github.com/sebastianbergmann/phpunit)   
[fluent-logger-php (github)](https://github.com/fluent/fluent-logger-php)   

---  
## gitについて
　  
gitの基本チュートリアルは [learnGit](http://k.swd.cc/learnGitBranching-ja/) で実際に動かしながら動作を学べます。  


リポジトリからローカルにチェックアウトするには以下のようにします。   

```
$ git clone --recursive git@github.com:cloudpack/apppack.git
$ cd apppack 
$ php composer.phar update
$ oil refine install
```

githubにコミットするには自分の環境のssh鍵をgithubに登録する必要があります。

---
## DocumentRoot
　  
publicディレクトリがドキュメントルートになります。  
プロジェクトをホスティングするにはいくつか方法がありますが、最も簡単なのはpublicディレクトリを/var/www/htmlからリンクすることです。  

```
# mv /var/www/html /var/www/html.org
# ln -s public /var/www/html
```

この場合、リンクを辿れるように、またpublicディレクトリ直下の.htaccessを使用できるようにwebサーバーの設定ファイルを書き換えます。

```
  Options Indexes FollowSymLinks
  #AllowOverride None
  AllowOverride All
```


---
## fuelphpについて
　  
fuelphpについての基本的な項目を記します。  
fuelphpはphp製の軽量MVCフレームワークです。  
Railsライクな構成で、composerなどモダンPHPのエコシステムを取り入れています。

#### ジェネレータ
fuelphpではいくつものジェネレータがあります。

* コントローラ
* モデル
* ビューモデル
* マイグレーション
* スキャフォルド
* タスク
* 設定ファイル

詳細は[oil generate](http://fuelphp.com/docs/packages/oil/generate.html)を参照
　  
　  
#### 設定ファイル
fuelphpでは設定ファイルをfuel/app/config/配下に配置します。  
localhostディレクトリはローカル開発用に追加したものです。コミットに含まれないようになっています。  
aws.phpはAWSを使用するプロジェクト用に追加したものです。   
ローカルで開発する場合は、localhostディレクトリ内にaws.phpをコピーして自分のcredentialを使用するなどしてください 
```
fuel/app/config/
├── aws.php       
├── config.php    
├── db.php        
├── development   // 開発サーバー用設定
├── localhost     // ローカル開発用設定
├── production    // 本番用設定
├── routes.php
├── staging       // ステージング用設定
└── test          // テスト用設定
```
　  
詳細は[config](http://fuelphp.com/docs/general/configuration.html)を参照

#### 環境設定 

fuel/app/confgの設定ファイルは環境ごとに切り替えることができます。  
環境変数FUEL_ENVの値に一致したサブディレクトリの設定が有効になります。  
有効になったサブディレクトリにある設定ファイルはconfig直下の同名の設定ファイルより優先されます。 

```
// httpdでの設定
SetEnv FUEL_ENV localhost 

// バッチ(oil)時の設定
env FUEL_ENV=localhost php oil -v

//ユニットテスト実行時の設定(fuel/app/phpunit.xml)
<server name="FUEL_ENV" value="localhost"/>
```

各設定をコード内で取得するには以下のようにします。

```php
//設定ファイル
aws.php
return array(
  'credential' => array(
	  'key' => 'xxxxxxxx',
		'secret' => 'yyyyyyyyyy',
	),
);


//awsファイル名のcredentialのkeyを取得
$key = Config::get('aws.credential.key');
//配列のまま取得することも可能
$credential = Config::get('aws.credential');
```

詳細は[environment](http://fuelphp.com/docs/general/environments.html)を参照
　  
　  

#### コントローラ

コントローラはモデルメソッドの呼び出しを行い、遷移の分岐や、表示するビューの設定を行います。
`oil g controller` コマンドでコントローラのひな形を作ることができます。    
クラスは fuel/app/classes/controller配下に配置します。  
コントローラでは1つのメソッドは1つのリクエストを受け付けます。  
コントローラにはいくつか種類があります。

* 通常のコントローラ
* テンプレートコントローラ：　表示するビューにヘッダフッタなど共通部品をつけて返します
* RESTコントローラ：　　　　 JSON、XMLなどの形式で返します
* ハイブリッドコントローラ：　RESTコントローラとテンプレートをメソッドごとに使い分けられます
 
詳細は[controller](http://fuelphp.com/docs/general/controllers/base.html)を参照
　  
　  

#### モデル

モデルはDB処理や演算/ロジックなど、業務ロジックに関する処理とデータモデルの管理などを行います。  
Orm\Modelを継承するとRDBMSを利用する場合にORMapperとして動作します。  
`oil g model`コマンドでモデルのひな形を作ることもできます。  
モデルは主にコントローラやタスクから呼び出されます。  　
AWSは主にモデル階層で利用することになります。　  
　  
詳細は[model](http://fuelphp.com/docs/general/models.html)を参照
　  
　  

#### ビュー

ビューはWEBアプリケーションのプレゼンテーション層です。
基本はPHPでラップしたHTMLファイルもしくはその一部です。
コントローラでView::forgeされたときの第2引数の連想配列の各要素を変数として扱うことができます。

詳細は[view](http://fuelphp.com/docs/general/views.html)を参照
　  
　
#### ルーティング

ルーティングとはURLとコントローラの各アクションメソッドへのマッピングです。
基本的には

```
http://www.yoursite.com/example/hoge/moge
```
  
というURLはexampleコントローラのhogeメソッドにmogeというパラメータを渡します。  
この基本ルールを変更することもできます。  
特別な設定をするにはfuel/app/config/routes.phpで設定したり、コントローラにハードコーディングを行います。

詳細は[routing](http://fuelphp.com/docs/general/routing.html)を参照

　
#### ロギング

##### ファイルログ
アプリケーションログはfuel/app/logs/yyyy/mm/dd配下に出力されます。  

```php
Log::error(print_r($data, true));
```

詳細は[log](http://fuelphp.com/docs/classes/log.html)を参照
 
##### fluentd
fluent-logger-phpを利用する場合は、unixドメインソケットを利用します。

```php
use Fluent\Logger\FluentLogger;
$logger = new FluentLogger("unix:///var/run/td-agent/td-agent.sock");
$logger->post("fluentd.test.follow", array("from"=>"userA", "to"=>"userB"));
```

詳細は[using-fluent-logger-php](http://docs.fluentd.org/articles/php#using-fluent-logger-php)を参照
 　
#### バッチ

fuelphpではバッチのことをタスクと呼びます。  
タスクはfuel/app/task/配下にコードを置きます。   
実行には`oil refine`コマンドを使用します。  

```
fuel/app/tasks/
  └── sample.php
  
//sample.php runメソッド(デフォルト)を実行する場合
$ oil refine sample

//sample.php hogeメソッドをパラメータを渡して実行する場合
$ oil refine sample:hoge moge
``` 

詳細は[oil refine](http://fuelphp.com/docs/packages/oil/refine.html)を参照
　  
　  


#### 管理画面

管理画面にはログイン機能とヘッダフッタなどのテンプレートをつけたCRUD画面、ログイン認証つきのコントローラなどの1CRUDセットを生成するスキャフォルド拡張の`oil g admin`コマンドを使用することができます。

```
oil g admin blog title:string article:text 
```
この機能はundocumentedです。
　  
　  
#### テスト

できる限りテストファーストで開発することをお勧めします。

###### ユニットテスト
fuelphpではphpunitの実行トリガをサポートしています。
テストコードはfuel/app/tests配下に配置します。

```
//fuelphp本体のテストを含めた全テストを実行
oil test

//アノテーションを指定して実行
oil test --group=app
```
上の2番目の例では、@appのアノテーションがあるテストだけを実行します。

各環境でのテスト実行
デフォルトで`oil test`はtest環境で実行されます。
FUEL_ENV=localhostで実行したい場合には以下のように行います。

```
$ cp fuel/core/phpunit.xml fuel/app/
$ vim fuel/app/phpunit.xml
---------
<server name="FUEL_ENV" value="test"/>
↓
<server name="FUEL_ENV" value="localhost"/>
---------
oil test --group=app
```

詳細は[unit testing](http://fuelphp.com/docs/general/unit_testing.html)を参照
　  
###### BDD
将来的にphpのBDDツールbehatを導入予定です。
　  
　  
---
## ライブラリの追加

このプロジェクトではライブラリの追加は全てcomposerを利用して下さい。
composer.jsonを編集して

```
php composer.phar update
```
でライブラリがインストールされます。

---
## サンプル
　  
WEBページとタスクにサンプルを一つずつ用意してあります。

```
//webページ
http://hostname/sample/upper/aiueo

//タスク
oil refine sample

//ユニットテスト
oil test --group=app
```

---

ここに記載した情報は全て基本的な内容で、他にも機能は沢山あります。詳細は各資料を参照してください。

[@memorycraft](http://www.facebook.com/memocra)
