const path = require("path");

module.exports = {
	entry: "./src/main.tsx", // エントリーポイント
	output: {
	path: path.resolve(__dirname, "dist"), // 出力先
	filename: "bundle.js" // 出力ファイル名
	},
	resolve: {
	extensions: [".ts", ".tsx", ".js"] // 解決する拡張子
	},
	module: {
	rules: [
		{
		test: /\.tsx?$/, // TypeScriptファイルにマッチ
		use: "ts-loader", // ts-loaderを使う
		exclude: /node_modules/ // node_modulesは除外
		}
	]
	},
	devtool: "source-map", // デバッグ用にソースマップを生成
	devServer: {
	static: path.join(__dirname, "dist"), // コンテンツの提供先
	compress: true, // gzip圧縮
	port: 9001, // 開発サーバーのポート
	mode: 'development', // 開発モードで動作させる
	watch: true, // ウォッチモードを有効にする
	}
};
