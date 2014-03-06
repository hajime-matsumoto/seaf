Seaf ライブラリ
======================

拡張、組み換え、カスタマイズのしやすさ、
ノウハウの蓄積のしやすさを第一に考えたライブラリです。

ディレクトリ構成
-------------------

src/Seaf ライブラリ
src/Seaf/Component コンポーネントライブラリ

コンポーネントの記述場所
---------------------

ワンファイルで完結する場合 src/Seaf/Componentn/XXX
そうでない場合 src/bundle/<name>/autoload.phpで
パスを追加する

	Seaf::di('autoLoader')->addNamespace(
		'Seaf\\Component\\Config',
		null,
		dirname(__FILE__).'/Config'
	);

