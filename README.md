Start server

```bash
symfony server:start
```

Stop server

```bash
symfony server:stop
```

Clear symfony cache

```bash
php bin/console cache:clear
```

Enable schedular

```bash
composer config extra.symfony.allow-contrib true
```

```bash
symfony console assets:install --symlink --relative public
```

Generate JWT SSL keys

#### must install open ssl

```bash
symfony console lexik:jwt:generate-keypair
```

Api Doc credentials

```
username: api-doc
password: apidoc123**
```
