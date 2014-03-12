言語対応
===========================

起動方法

設定を追加
```yaml
default:
  dirs:
    p18n: $dirs.root$/etc/i18n
  modules:
    - p18n
```

```php
# ヘルパーを使わない場合
Environment::p18n();

# ヘルパーを使う場合
$env = new Environment();
$env->p18n()->importHelper($env);
$env->t();
```

```php
$env->t()->site->title;
$env->t()->count_pl(0);
$env->t()->count_pl(1);
$env->t()->count_pl(2);
$env->t()->myname('はじめ');
```
