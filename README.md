# Руководство по настройке проекта на сервере

Все инструкции ниже приводятся для операционной системы Ubuntu.

Перед началом настройки убедитесь, что у вас установлен 
[NGINX](https://nginx.org/ "NGINX"), 
[Composer](https://getcomposer.org/ "Composer") 
(желательно 
[установить Composer глобально](https://getcomposer.org/doc/00-intro.md#globally "Глобальная установка Composer")) и 
[PHP-FPM](https://php-fpm.org/ "PHP-FPM") (версии PHP 7+, в данный момент 
при разработке используется версия PHP 7.2).

**Примечание**: установка `PHP-FPM` выполняется следующей командой на примере 
версии PHP 7.2:

```bash
sudo apt-get install php7.2-fpm
```

## Конфигурация NGINX:

В файл `nginx.conf` вставьте следующее:

```nginx
user www-data www-data;

events {}

http {
  include /etc/nginx/mime.types;

  server {
    client_max_body_size 1m;

    listen 80;
    server_name ; # Глобальный адрес
    root ; # Локальный путь к корню проекта

    location / {
      include fastcgi_params;
      include fastcgi.conf;
      fastcgi_pass 127.0.0.1:9000;
      try_files $uri /index.php?$args;
    }

    location /application/assets/ {
      autoindex on;
    }

    location /uploads/ {
      autoindex on;
    }
  }
}
```

## Необходимые модули и расширения:

- `imagick`:

  Установка выполняется следующей командой:

  ```bash
  sudo apt-get install php-imagick
  ```

- `mbstring`:

  Установка выполняется следующей командой:

  ```bash
  sudo apt-get install php-mbstring
  ```

- `fileinfo`:

  В конфигурационном файле `php.ini` для `PHP-FPM` (по умолчанию находится в 
  директории `/etc/php/ВЕРСИЯ_PHP/fpm/`) уберите `;` перед строчкой 
  `extension=fileinfo`.

- `sqlite3` и `PDO`:

  В конфигурационном файле `php.ini` для `PHP-FPM` (по умолчанию находится в 
  директории `/etc/php/ВЕРСИЯ_PHP/fpm/`) уберите `;` перед строчками 
  `extension=pdo_sqlite` и `extension=sqlite3`.

  Возможно, придётся также установить `SQLite 3` с помощью `apt-get`:

  ```bash
  sudo apt-get install sqlite3 php-sqlite3
  ```

- `Memcached`:

  Установка выполняется следующей командой:

  ```bash
  sudo apt-get install memcached php-memcached
  ```

## Генерация пары ключей RS256 для JWT

Проект использует технологию JWT, выпускающую токены для авторизации. Для 
`refresh_token` используется алгоритм RS256, для которого необходимо 
сгенерировать пару ключей (публичный и приватный), в корне 
проекта последовательно выполнив следующие команды:

```bash
mkdir application/lib/rs256

ssh-keygen -t rsa -b 4096 -m PEM -f application/lib/rs256/jwtRS256.key
# Оставьте пароль пустым

openssl rsa -in application/lib/rs256/jwtRS256.key -pubout -outform PEM -out application/lib/rs256/jwtRS256.key.pub
```

## Установка зависимостей

Для того, чтобы установить все зависимости в проекте, в корне проекта выполните 
команду:

```bash
composer install
```

## Миграция на последнюю версию базы данных

Для управлением миграциями баз данных в проекте используется 
[Phinx](https://phinx.org/ "Phinx"). Чтобы мигрировать на последний вариант 
структуры базы данных, используете следующую команду в корне проекта:

```bash
vendor/bin/phinx migrate
```