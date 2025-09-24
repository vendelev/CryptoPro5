# Пример сборки контейнера с CryptoPro 5 и PHP 8

## Настройка
1. Перед запуском сборки надо скопировать [.env.example](.env.example) в .env и прописать в нем значение для LICENSE (это ключ для CryptoPro 5).
2. В папку [docker/keys](docker/keys) скопируйте папку со своим сертификатом (например, это папка pfx-6789.000), которым будете подписывать файлы. Подробно об экспорте написано здесь https://pushorigin.ru/cryptopro/real-cert-crypto-pro-linux

## Запуск
Далее, в корне проекта выполнить команды:
- `docker compose build`
- `docker compose up -d`

Этапы сборки описаны в [Dockerfile](docker/Dockerfile):
1. Сначала устанавливается CryptoPro, прописывается лицензия и добавляется ваш сертификат
2. Настраивается образ PHP и в него копируются файлы для запуска CryptoPro (так сделано для уменьшения размера образа)

## Примеры 
После старта контейнера будут доступны 2 endpoint:

- Подписание: http://localhost:8095/sign
  Описание API: `curl -X POST "http://localhost:8095/sign" -H  "Content-Type: multipart/form-data" -F "file=@README.md;type=text/html"`. <br>
  В случае успеха будет выведен json:
  ```json
  {"status":200,"message":"MIAGCSqGSIb3DQEHAqCAMIACAQExDDAKBggqhQMHAQECA............nAAA="}
  ```

- Проверка подписи http://localhost:8095/verify
  Описание API: `curl -X POST "http://localhost:8095/verify" -H  "Content-Type: multipart/form-data" -F "file=@compose.yaml;type=text/html"  -F "sign=@compose.yaml.sig;type=text/html"`. <br>
  В случае успеха будет выведен json:
  ```json
  {"status":200,"message":""}
  ```
  В случае ошибки:
  ```json
  {"status":400,"message":"[ErrorCode: 0x200001f9]"}
  ```

