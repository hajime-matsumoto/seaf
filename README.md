実行環境の整方
=========================

- 必須 PHP >= 5.4.0 
- ruby > 1.9
- nodejs

------------------------
```sh
sudo add-apt-repository ppa:ondrej/php5
```

ruby (for sass with compass)
------------------------
```sh
sudo apt-get install ruby1.9 rubygems
sudo gem install compass sass
```

nodejs (for coffee)
------------------------
sudo npm install -g coffee-script

基本的な概念
=========================

Environmentパターン
-------------------------
動的メソッド機能、DIコンテナ機能、イベント機能、変数コンテナ機能
を集約したパターン。

```php
$env = new Environment( );

// 変数コンテナ機能
$env->set('key', 'value');
echo $env->get('key'); # valueと出力される

// DIコンテナ機能
$env->registry('container','Seaf\Data\Container\ArrayContainer');
$container = $env->Container(); # ArrayContainerオブジェクトが返る

// イベント機能
$env->on('event.name', function ( ) {
	echo 'hi';
});
$env->trigger('event.name'); # hiと出力される

// 動的メソッド機能
$env->map('sayHello', function ( ) {
	echo 'hello';
});
$env->sayHello( ); # helloと出力される
```


Seaf
-------------------------
シングルトンパターンで一つのEnvironmentパターンを持ち、
DIコンテナはデフォルトでCore\Componentを読み込む
Seafで定義されていない呼び出しは全てEnvironmentへフォワードされる

Seafでしかつかわれないメソッド

```php
Seaf::init(プロジェクトのパス, 実行モード); # 初期化処理
Seaf::enmod(モジュール名); # モジュールを有効化する
```

コアコンポーネント
--------------------------

```php
Seaf::コンポーネント名( ); # コンポーネントを呼び出す
                           # もし、コンポーネントがhelperメソッドを持つ場合
                           # helperメソッドを呼び出す
```

+ AutoLoader       : クラスローダ
+ Config           : コンフィグハンドラ
+ Cookie           : クッキー処理
+ FileSystem       : ファイルシステムヘルパ
+ Globals          : スーパーグローバル変数の管理
+ Helper           : ヘルパーコレクション
+ Logger           : ロガーハンドラ
+ ReflectionClass  : リフレクションクラス拡張
+ ReflectionMethod : リフレクションメソッド拡張
+ Secure           : セキュリティに関するUtility
+ System           : システムに関するUtility
