# Laravel9-TDD
このリポジトリは Laravel 9 を使用したテスト駆動開発 (TDD) の学習用リポジトリです。

## 環境構築
### 必要なもの
- src ディレクトリ配下に .env ファイルを用意してください。
- src ディレクトリ配下に .env.testing ファイルを用意してください。

上記の2つのファイルを用意した後、以下のコマンドを実行して開発環境の Docker コンテナを作成・起動してください。
```shell
docker-compose up -d --build
```

コンテナの起動が完了したら、php-fpm コンテナで composer install コマンドを実行します。

```shell
docker exec -it php-fpm bash -c "composer install"
```

次に、アプリケーションに必要なデータをセットアップします。

```shell
docker exec -it php-fpm bash -c "php artisan migrate:fresh --seed"
```

[http://localhost/](http://localhost/)へアクセスし、ブログ一覧が表示されれば環境構築は完了です。

もし /var/www/storage に書き込み権限がないというエラーが表示された場合は、以下のコマンドを実行してください。

```shell
docker exec -it php-fpm bash -c "chown -R www-data:www-data /var/www/storage"
```

## phpmyadmin
[phpmyadmin](http://localhost:8080)を使用して、アプリケーションのデータベースを GUI で閲覧できます。


