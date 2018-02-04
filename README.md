CONFIGURATION
-------------

### Database

Edit the file `config/db.php` with real data, for example:

```php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=yii2basic',
    'username' => 'root',
    'password' => '1234',
    'charset' => 'utf8',
];
```

Then run ./yii migrate
and then create admin account ./yii user/create-admin name password email

### Mailer

Edit the file `config/mailer.php` with real data, for example:

```php
[
    'class' => 'yii\swiftmailer\Mailer',
    'transport' => [
        'class' => Swift_SmtpTransport::class,
        'host' => 'smtp.gmail.com',
        'username' => 'username',
        'password' => 'password',
        'port' => '587',
        'encryption' => 'tls',
    ],
]
```

### Url generation in console app

Edit the file `config/console.php` with real data according to your hosting, for example:

```php
[
    'urlManager' => [
                'hostInfo' => 'http://berrynet.ddns.net:88',
                'baseUrl' => '',
                'scriptUrl' => '/index.php',
            ],
]
```

### Background news sender must be executed every 5 minutes, for example by cron:

```bash
crontab -e

*/5 * * * * /var/www/testphp/newspaper/yii news/send
```


TESTING
-------

Tests are located in `tests` directory. They are developed with [Codeception PHP Testing Framework](http://codeception.com/).
There are 2 test suites:

- `unit`
- `functional`

Tests can be executed by running

```
vendor/bin/codecept run
```

DEMO
------

Demo available: http://berrynet.ddns.net:88/
credentials for admin user: admin/admin
