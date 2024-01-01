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