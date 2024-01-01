# Laravel テスト

このプロジェクトはLaravelフレームワークを使用しています。開発を始める前に、環境構築を行ってください。


## 環境構築
**phpMyAdminのセッションディレクトリの設定**

phpMyAdminを使用するためには、セッションデータを保存するディレクトリが必要です。以下のコマンドで phpmyadmin/sessions/ ディレクトリを作成し、適切なパーミッションを設定してください。
```shell
phpmyadmin/sessions/
```

**Laravelのストレージとキャッシュ用ディレクトリの設定**

Laravelのストレージとキャッシュ用ディレクトリは、Webサーバーからアクセス可能である必要があります。以下のコマンドを実行して、これらのディレクトリの所有権とパーミッションを適切に設定してください。
```shell
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache && chmod -R 775 /var/www/storage /var/www/bootstrap/cache
```

http://localhost/ にアクセスしてログイン画面が表示されれば構築完了です。

もし、Warning: require(/var/www/public/../vendor/autoload.php): Failed to open stream: No such file or directory in /var/www/public/index.phpと表示された場合は以下コマンド実行してください
```shell
docker exec -it php-fpm bash -c "composer install"
```


キャッシュをクリアする(.envの更新が反映されないとき)
```shell
php artisan cache:clear && php artisan config:clear 
```


## テスト実行方法

全てのテストを実行
```shell
php artisan test
```

部分一致
```shell
php artisan test --filter クラス名やメソッド名
```

ファイル指定
```
php artisan test tests/Feature/ExampleTest.php
```

フォルダ指定
```shell
php artisan test tests/Feature
```

スイート名指定
```shell
php artisan test --testsuite Feature
```

コンテナの外から部分一致テストを実行する
```shell
docker exec -it php-fpm bash -c "php artisan test --filter TOPページで、ブログ一覧が表示される"
```



test用のファイルを生成するコマンド
```shell
php artisan make:test Http/controllers/PostListControllerTest
```


## マイグレーション
マイグレーションを実行
```shell
docker exec -it php-fpm bash -c "php artisan migrate:fresh --seed"
```

前回のマイグレーションに戻す
```shell
docker exec -it php-fpm bash -c "php artisan migrate:rollback"
```

seederでデータ投入(データベースのテーブルを削除し、その後新しいテーブルを作成)
```shell
docker exec -it php-fpm bash -c "php artisan migrate:fresh --seed"
```

