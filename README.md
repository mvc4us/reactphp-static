# reactphp-static

A simple middleware for serving static files with ReactPHP

## Installation

```shell
composer require mvc4us/react-static
```

## Usage

Use it in your ReactPHP like a normal middleware, passing in an array of base url(s) pointing to associated webroot(s).
All the files under any webroot (including subdirectories) will be served.
```php
new Mvc4us\ReactStatic\StaticServer([
    "/" => "/wwwroot/domain/public",
    "/docs" => "/documents/from/other/directory"
]);
```

Optionally to exclude some files you can define an array of shell patterns as a second parameter:

```php
new Mvc4us\ReactStatic\StaticServer(
    ["/" => "/webroot"],
    ["*.php", ".htaccess", "*private*"]
);
```

The middleware will serve any static files if they exist or return 404 response if it is excluded. If a file does not exist for the requested path, it continues processing next middleware, letting you run the rest of your application.

Enjoy!
