構成
====================

Kernel
: システムとのやり取りをする

Seaf
: アプリケーションを管理する

Core\Environment\Environment
: アプリケーションのワークスペース

Core\Pattern
: 汎用的なパターン

Config\Config
: 設定保持オブジェクト

Log\Logger
: Logの投げ込みよう

Log\Writer
: Logの書き込み

モジュール
=========================

```php
Seaf::enmod('p18n');

class Some extends Environment
{
	function initEnvironment () 
	{
		$this->p18n()->importHelper($this);
		$this->t()->site->title;
	}
}
```
