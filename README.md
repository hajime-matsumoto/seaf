Seafライブラリ
=========================

拡張可能な軽量フレームワーク
-------------------------

機能
========================

Environmentクラス
------------------------
Seafの基本となるクラス。

デザインパターン的に言うと、
DIパターン + コマンドパターン + ファクトリパターン + α組み合わせたもの。

```php
$env = new Environment();
$env->map('test', function( ) {
	echo 'test called';
});
$env->test(); # test calledと出力される
$env->register('SomeObject', 'SomeClass');
$env->SomeObject(); # SomeClass のインスタンスが返却される
```
このように、インスタンス、メソッドをランタイム拡張してゆきプログラム
の母体を集中かんりするクラス。


コード記述なしで動的な拡張が可能。
------------------------
ネームスペースベースで拡張ライブラリの管理ができる。
上記のregisterメソッドを使わずに、ネームスペースだけで拡張が可能

```php
class Seaf\Environment\Component\MyClass{ }

$env = new Environment();
$env->myClass(); # これでインスタンスが返る
```

Environmentを拡張したクラス
--------------------------

* Seaf\Web\Application  ウェブアプリケーション用のEnvironment
* Seaf\Console\Application  コンソールアプリケーション用のEnvironment

```php
# すごくシンプルな使い方
# / でHelloWorld
# /sayName/name/hajime で Hello hajime と出力される
Seaf::web( )->route('/', function ( ) {
	echo 'Hello World';
})->route('/sayName/name/(@name)',function($name) {
	echo "Hello $name";
})->run();
```

```php
# Viewを使う場合
$web = Seaf::web(); # Seaf\Web\Application
$web->config()
	->set('root.path',__DIR__)
	->set('view.enable',true)
	->set('view.path','./views');

$view = $web->view(); # Seaf\View\View
$view->set('engin','twig');

$web->route('/', function ( ) use ($view) {
	$view->render('index');
});

$web->run();
```

ケーススタディ
==============================

SASS & COFFEE SCRIPTを使う
-----------------------------

ログをとる
-----------------------------

中規模なシステムを作る
----------------------------
