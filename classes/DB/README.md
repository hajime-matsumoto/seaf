データベース関連
=============================

DBには2種類ある。
-----------------------------

+NoSQL系:mongodb
+SQL系:postgre mysql

スクラッチ
-----------------------------

DB種類の操作差を吸収したい。
```php
// SQL系
$q = DB()->quriteria(array(
			'a'=>['=','b'],
			'or',
			'b'=>['=','c']
));
DB()->user_pre->select($q);

DB()->setHandler('sql', 'mysql://root:degaunjue@localhost:3301/seaf_test');
DB()->setHandler('nosql', 'mongodb:///test');

DB('nosql')->sample->insert(array('a'=>'b'));
DB('sql')->sample->insert(array('a'=>'b'));
DB('sql')->addSchema(Schema::factory(aray()));
DB( )->setFilter('insert', function ($request) {
	if ($request->process == null) return false;

	if ($request->table == 'tmp') {
		DB('nosql')->insert($request);
		return $request->process = null;
	}
});

// Databaseの初期化

DB( )
->connectMap(
	'nosql' => 'mongodb:///test',
	'sql' => 'mysql://root:degaunjue@localhost:3301'
)
->handlerMap(
	'access_log' => 'nosql',
	'user_pre' => ['sql', 'UserPreModel::schema()']
)
->cacheStorage(
	'type' => 'memcache',
	'servers' => [localhost:1191]
)->schema(
	'user_pre' => 'UserPreModel::schema( )'
);

// DBリクエストの作成
DB()->user_pre->newRequest('insert')->params([
	'user_id' => 1,
	'name' => 'hajime'
])->execute( );

DB( )->access_log->insert(['user'=>1,'path'=>'/test']);
DB( )->user_pre->insert(['id'=>1,'name'=>'hajime']);
```
