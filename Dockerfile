# PHPの公式イメージをベースにする (ここではPHP 8.2とApacheを使用)
FROM php:8.2-apache

# 必要な拡張機能をインストール
RUN docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable pdo_mysql

# Apacheの設定（URLリライトなどが必要なければこのままでOK）
# RUN a2enmod rewrite

# アプリケーションファイルをコンテナのウェブサーバーのルートディレクトリにコピー
# ホスト側のカレントディレクトリ（WBCフォルダ）にあるファイルをコピー
COPY . /var/www/html/

# --- ファイル所有権の修正 ---
# /var/www/html ディレクトリの所有権を www-data ユーザーに変更
# これにより、Apacheがマウントされたファイルにアクセスできるようになる
RUN chown -R www-data:www-data /var/www/html

# Apacheのデフォルトのドキュメントルートを調整する場合があるが、
# php:apache イメージでは /var/www/html がデフォルトなので、通常はこのままでOK