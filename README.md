## Vhost Manager 1.0

I have created this small php symfony based command line application to scratch my own itch.

It'll allow user to create virtual host using a single command, without going into the host files.

The application is only **Windows** user.

### Install
```bash
git clone https://github.com/vivekdhumal/Vhost-Manager.git

cd Vhost-Manager

composer install
```

### Configuration

At the root of the application you ll get the `config.php` file where you can change the path of your host files as per your local setup.
```php
return [
    /**
     * This apache httpd-vhosts.conf file path.
     */
    'apache_host_path' => 'E:\xampp\apache\conf\extra\httpd-vhosts.conf',
    /**
     * Your windows hosts file path, generaly it'll remain same.
     */
    'windows_host_path' => 'C:\Windows\System32\drivers\etc\hosts',
    /**
     * Your workspace path, this could be your xamp or wamp path.
     * for wamp it'll look like E:\wamp\www
     */
    'workpace_path' => 'E:\xampp\htdocs',
];
```

### How To Create Virtual Host Using Vhost Manager?
Well its super simple, suppose you need to host `test/public` folder from your `htdocs` or `www` directory, (based on your local setup) to the `testing.dev` domain, then you need to go to the app `Vhost-Manager` & run the following command.
```bash
php vhost create:host -r "test/public" -d "testing.dev"
```
For help run the following command, it will show you the description about the input options.
```bash
php vhost create:host --help
```

Thank you.
