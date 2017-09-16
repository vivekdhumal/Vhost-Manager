## Vhost Manager 1.0

I have created this small php symfony based command line application to scratch my own itch.

It'll allow user to create virtual host using a single command, without going into the host files.

The application is only **Windows** user.

### Requirements
php >= 7.0.0

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
    /**
     * local ip address.
     */
    'local_ip' => '127.0.0.1',
];
```

### Create Host
Go to the app `Vhost-Manager` & run the following command.
```bash
php vhost create "test/public" "testing.dev"
```
This will take document root as first argument and domain as second argument.

### List Hosts
You can view all your virtual hosts right from your terminal, just hit the following command.
```bash
php vhost all
```

### Remove Host
You can also remove virtual hosts from your host files, just hit the following command.
```bash
php vhost remove "testing.dev"
```
This will take domain as a argument.

### License
The Vhost Manager is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
