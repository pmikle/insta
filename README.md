# Sender sweet messages on Instagram
## Annotation
One day, my wife told me that I rarely send her cute messages just like that. And I decided .. to write a script for sending messages to Intsagram on a schedule :-)
Please do not use this sample to send out unnecessary messages.
## Quick start
- clone repository
```bash
$ git clone git@github.com:pmikle/insta.git
```
- install composer dependencies
```bash
composer install
```
- set schedule
In _src\Config.php_ param _$available_message_sending_periods_.

Example:
```php
public static $available_message_sending_periods = [
    ['09:15:00', '10:15:00'],
    ['15:15:00', '16:15:00'],
];
```
    
- launch with parameters
```bash
php bin/console instagram-send-message <parameter:targetUid> <parameter:login> <parameter:pass> <parameter:message>
```
## Arguments
**targetUid**

Required parameter.

**login**

User sender login. Optional (you may set this params in _src\Config.php_ _$login_).

**pass**

User sender password. Optional (you may set this params in _src\Config.php_ _$password_).

**message**

Message for sending.

**verbose**

Available verbose mode -v.

## Usage

- adding message array

In file _static/messages.php_.

Example:
```php
<?php
return [
    "Message 1",
    "Message 2",
];
```
- add task in crontab

```bash
* * * * * php <path_to_script>/bin/console instagram-send-message <targetUid> -v >> <log_path>/<log_filename>
```  

## Automatic verification of script execution result

Code to complete the last operation in *nix OS:

```bash
$ $?
```

* true or 0 - there are problems
* false or 1 - successful

## Custom config

For special cases, you can create your own config on top of the standard in the file _custom_config.php_ in root project directory. And override the already set properties of the main file.

Example:

```php
<?php
\App\Config::$login = 'Not_basic_login';
```