# Генерация пары ключей RS256 для JWT

Перейдите в корень проекта и последовательно наберите следующие команды:

```bash
mkdir application/lib/rs256

ssh-keygen -t rsa -b 4096 -m PEM -f application/lib/rs256/jwtRS256.key
# Оставить пароль пустым

openssl rsa -in application/lib/rs256/jwtRS256.key -pubout -outform PEM -out application/lib/rs256/jwtRS256.key.pub
```