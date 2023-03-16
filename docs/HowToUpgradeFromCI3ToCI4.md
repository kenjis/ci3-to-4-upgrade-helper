# How to Upgrade from CI3 to CI4

## Table of Contents

<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->

- [Read CodeIgniter4 User Guide](#read-codeigniter4-user-guide)
- [Install CodeIgniter4](#install-codeigniter4)
- [Install ci3-to-4-upgrade-helper](#install-ci3-to-4-upgrade-helper)
- [Config](#config)
  - [config.php](#configphp)
  - [Routing](#routing)
  - [autoload.php](#autoloadphp)
  - [Custom Config Files](#custom-config-files)
  - [Other Config Files](#other-config-files)
  - [app/Config/View.php](#appconfigviewphp)
- [Hooks](#hooks)
- [Database Migrations](#database-migrations)
  - [Copy Migration files](#copy-migration-files)
  - [Add Namespace and Use statement](#add-namespace-and-use-statement)
  - [$this->db](#this-db)
  - [Table migrations](#table-migrations)
- [Database Seeding](#database-seeding)
  - [Copy Seeder files](#copy-seeder-files)
  - [Add Namespace and Use statement](#add-namespace-and-use-statement-1)
  - [$this->db](#this-db-1)
  - [$this->call()](#this-call)
- [Controllers](#controllers)
  - [Copy Controller files](#copy-controller-files)
  - [Add Namespace and Use statement](#add-namespace-and-use-statement-2)
  - [_output()](#_output)
  - [MY_Controller](#my_controller)
- [Models](#models)
  - [Copy Model files](#copy-model-files)
  - [Add Namespace and Use statement](#add-namespace-and-use-statement-3)
- [Libraries](#libraries)
  - [Copy Library files](#copy-library-files)
  - [Add Namespace](#add-namespace)
  - [Form_validation](#form_validation)
    - [Validation Rules](#validation-rules)
  - [Pagination](#pagination)
- [Views](#views)
  - [Copy View files](#copy-view-files)
- [Helpers](#helpers)
  - [Copy Helper files](#copy-helper-files)
  - [URL Helper](#url-helper)
    - [redirect()](#redirect)
    - [base_url()](#base_url)
    - [url_title()](#url_title)
- [Common Functions](#common-functions)
  - [show_error()](#show_error)
- [Language files](#language-files)
- [Writable Paths](#writable-paths)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->

## Read CodeIgniter4 User Guide

CodeIgniter 4 is a rewrite of the framework. It is not an exaggeration to say that it is a new framework. Please read the User Guide carefully to get an overview.

- [Upgrading from 3.x to 4.x](https://codeigniter4.github.io/CodeIgniter4/installation/upgrade_4xx.html)
- [CodeIgniter4 Overview](https://codeigniter4.github.io/CodeIgniter4/concepts/index.html)
- [General Topics](https://codeigniter4.github.io/CodeIgniter4/general/index.html)

## Install CodeIgniter4

See <https://codeigniter4.github.io/CodeIgniter4/installation/index.html>.

> **Note** 
> Use 4.3.1 or later. [ci4-app-template](https://github.com/kenjis/ci4-app-template) can be used.

## Install ci3-to-4-upgrade-helper

```
$ composer require kenjis/ci3-to-4-upgrade-helper:1.x-dev
```

## Config

### config.php

1. Migrate `application/config/config.php` to `app/Config/App.php` manually. You can set your own values like base_url with `.env` file.

### Routing

1. Migrate `application/config/routes.php` to `app/Config/Routes.php` manually.

See <https://codeigniter4.github.io/CodeIgniter4/incoming/routing.html#setting-your-own-routing-rules>.

### autoload.php

1. CI4 does not have CI3's “Auto-load” feature, except helper autoloading.
2. To autoload helpers, add your `$autoload['helper']` value in `autoload.php` config to the property `$helpers` in `app/Config/Autoload.php`. This is CI4's feature.
3. To autoload libraries, add your `autoload.php` config in the property `$libraries` in `app/Controllers/BaseController.php`. This feature is provided by *ci3-to-4-upgrade-helper*.

Example:
```diff
--- a/app/Controllers/BaseController.php
+++ b/app/Controllers/BaseController.php
@@ -20,6 +20,17 @@ use Psr\Log\LoggerInterface;
 
 class BaseController extends Controller
 {
+    /**
+     * CI3's $autoload['libraries']
+     *
+     * @var array
+     */
+    protected $libraries = [
+        'database',
+        'session',
+        'form_validation',
+    ];
+
     /**
      * An array of helpers to be loaded automatically upon
      * class instantiation. These helpers will be available
```

### Custom Config Files

1. Convert custom config files to Config classes manually.

See <https://codeigniter4.github.io/CodeIgniter4/general/configuration.html#creating-configuration-files>.

2. Replace the CI3 config name with the new config classname in your code.

Example:
```php
$this->config->load('config_shop');
```
↓
```php
$this->config->load('ConfigShop');
```

### Other Config Files

1. Migrate remaining `application/config/*.php` to `app/Config/*.php` manually. You can set your own values like database password with `.env` file.

### app/Config/View.php

1. If you use `$this->config` in view files, you need to add the following code.

```diff
--- a/app/Config/View.php
+++ b/app/Config/View.php
@@ -3,9 +3,12 @@
 namespace Config;
 
 use CodeIgniter\Config\View as BaseView;
+use Kenjis\CI3Compatible\Traits\View\ThisConfigInView;
 
 class View extends BaseView
 {
+   use ThisConfigInView;
+
    /**
     * When false, the view method will clear the data between each
     * call. This keeps your data safe and ensures there is no accidental
```

## Hooks

1. Migrate `application/config/hooks.php` to `app/Config/Events.php` or `app/Config/Filters.php` manually.
2. Migrate `application/hooks/*` to *Events* or *Controller Filters* manually.

See <https://codeigniter4.github.io/CodeIgniter4/extending/events.html> or <https://codeigniter4.github.io/CodeIgniter4/incoming/filters.html>.

## Database Migrations

CI4 has built-in Database Migrations. *ci3-to-4-upgrade-helper* provides `CI_Migration` class that extends CI4's Migration class.

### Copy Migration files

1. Copy migration files to `app/Database/Migrations/`.
2. Rename migration file names. See <https://codeigniter4.github.io/CodeIgniter4/dbmgmt/migration.html#migration-file-names>.
3. Rename migration class names. Remove `Migration_` and change to CamelCase.

### Add Namespace and Use statement

1. Add `namespace App\Database\Migrations`.
2. Add `use Kenjis\CI3Compatible\Library\CI_Migration;`

Example:
```php
namespace App\Database\Migrations; // Add

use Kenjis\CI3Compatible\Library\CI_Migration; // Add

class CreateBbs extends CI_Migration
{
    ...
}
```

### $this->db

`$this->db` in migration files is CI4's Database connection. If you want to use CI3 compatible `$this->db`, replace it with `$this->db_` which *ci3-to-4-upgrade-helper* provides.

### Table migrations

The table `migrations` in CI3 is incompatible. The definition of the table for CI4 MySQL is:
```mysql
create table migrations
(
    id        bigint unsigned auto_increment primary key,
    version   varchar(255)     not null,
    class     varchar(255)     not null,
    `group`   varchar(255)     not null,
    namespace varchar(255)     not null,
    time      int              not null,
    batch     int(11) unsigned not null
)
    charset = utf8;
```

If you want to use the database you have been using in CI3:
1. You must drop (or rename) the table `migrations`.
2. Create a new table `migrations` for CI4.
3. Run the CI4 migration in the development environment or so, to create the migration data.
4. Import the new migration data of CI4 into your production `migrations` table.

## Database Seeding

CI4 has built-in [Database Seeding](https://codeigniter4.github.io/CodeIgniter4/dbmgmt/seeds.html). *ci3-to-4-upgrade-helper* provides `Seeder` class that is based on Seeder class in [ci-phpunit-test](https://github.com/kenjis/ci-phpunit-test) and extends CI4's Seeder class.

### Copy Seeder files

1. Copy seeder files to `app/Database/Seeds/`.

### Add Namespace and Use statement

1. Add `namespace App\Database\Seeds`.
2. Add `use Kenjis\CI3Compatible\Library\Seeder;`

Example:
```php
namespace App\Database\Seeds; // Add

use Kenjis\CI3Compatible\Library\Seeder; // Add

class ProductSeeder extends Seeder
{
    ...
}
```

### $this->db

`$this->db` in seeder files is CI4's Database connection. If you want to use CI3 compatible `$this->db`, replace it with `$this->db_` which *ci3-to-4-upgrade-helper* provides.

### $this->call()

`$this->call()` in seeder files is the method of CI4's Seeder. If you want to use *ci-phpunit-test* compatible `$this->call()`, replace it with `$this->call_()`.

## Controllers

### Copy Controller files

1. Copy `application/controllers/*` to `app/Controllers/*`.
2. Rename the sub-folder names so that only the first letter is uppercase.

### Add Namespace and Use statement

1. Add `namespace App\Controllers;`.
2. Add `use Kenjis\CI3Compatible\Core\CI_Controller;`

Example:
```php
namespace App\Controllers; // Add

use Kenjis\CI3Compatible\Core\CI_Controller; // Add

class News extends CI_Controller
{
    ...
}
```

### _output()

1. CI4 does not have `_output()` method.
- Use Controller Filters. See <https://codeigniter4.github.io/CodeIgniter4/incoming/filters.html>.

### MY_Controller

1. Copy `application/core/MY_Controller` to `app/Controllers/MY_Controller`.
2. Add Namespace and Use statement as other controllers.

## Models

### Copy Model files

1. Copy `application/models/*` to `app/Models/*`.
2. Rename the sub-folder names so that only the first letter is uppercase.

### Add Namespace and Use statement

1. Add `namespace App\Models;`.
2. Add `use Kenjis\CI3Compatible\Core\CI_Model;`

Example:
```php
namespace App\Models; // Add

use Kenjis\CI3Compatible\Core\CI_Model; // Add

class News_model extends CI_Model
{
   ...
}
```

## Libraries

### Copy Library files

1. Copy `application/libraries/*` to `app/Libraries/*`.
2. Rename the sub-folder names so that only the first letter is uppercase.

### Add Namespace

1. Add `namespace App\Libraries;`.

Example:
```php
namespace App\Libraries; // Add

class Seeder
{
   ...
}
```

### Form_validation

1. CI4 has View templates to display errors.
- The CI3 methods to customize error output are not supported.
- Create your own templates, and configure it in `app/Config/Validation.php`.
- See <https://codeigniter4.github.io/CodeIgniter4/libraries/validation.html#customizing-error-display>.
- `error_string()` is also not supported. Use `error_array()` instead, if you really need it.

Example:
```php
array_reduce($this->form_validation->error_array(), function ($carry, $item) {
    $carry .= '<p>'.$item.'</p>';
    return $carry;
});
```

- If you need more than one template for `list` or `single`, please use CI4's native methods and pass your template name.

Examples:
```php
<?= \Config\Services::validation()->listErrors('my_list') ?>
```

```php
<?= \Config\Services::validation()->showError('username', 'my_single') ?>
```

2. CI4 has no [Callbacks](https://codeigniter.com/userguide3/libraries/form_validation.html#callbacks-your-own-validation-methods) nor [Callable](https://codeigniter.com/userguide3/libraries/form_validation.html#callable-use-anything-as-a-rule).
- But you can create a validation rule with [Closure](https://codeigniter4.github.io/CodeIgniter4/libraries/validation.html#using-closure-rule).
- Or Create your own Rule classes, and configure it in `app/Config/Validation.php`. See <https://codeigniter4.github.io/CodeIgniter4/libraries/validation.html#creating-custom-rules>.
3. `set_message()`
- If you create a custom rule, use the second param `&$error` and set the error message. See <https://codeigniter4.github.io/CodeIgniter4/libraries/validation.html#creating-custom-rules>.
- Otherwise, use `CI_Form_validation::setError(string $field, string $error)` that *ci3-to-4-upgrade-helper* provides. 
4. CI4's `Validation` never changes your data.
- If you set the rule `trim|required`, the value during validation is trimmed, but the value after validation is not trimmed. You must trim it by yourself.

#### Validation Rules

1. Setting validation rules providing a constructor param array is not supported. Please convert it to `Config\Validation` class. See <https://codeigniter4.github.io/CodeIgniter4/libraries/validation.html#saving-sets-of-validation-rules-to-the-config-file>.
2. CI4's format rules like `alpha_numeric`, `valid_email` do not permit empty string. If you want to permit empty, add the rule `permit_empty`.

### Pagination

1. CI4 has View templates for Pagination.
- The CI3 configurations to customize pagination links are not supported.
- Create your own templates, and configure it in `app/Config/Pager.php`.
- Use `$pager->hasNextPage()` and `$pager->getNextPage()` instead of `$pager->hasNext()` `$pager->getNext()` for the next page link.
- A sample file is included in `src/CI3Compatible/Views/Pager/`. You could use it.

*app/Config/Pager.php*
```php
    public $templates = [
        'default_full'   => 'Kenjis\CI3Compatible\Views\Pager\default_full',
        'default_simple' => 'CodeIgniter\Pager\Views\default_simple',
        'default_head'   => 'CodeIgniter\Pager\Views\default_head',
    ];
```

- See <https://codeigniter4.github.io/CodeIgniter4/libraries/pagination.html#customizing-the-links>.
2. CI4 uses the actual page number only. You can't use the starting index (offset) for the items which is the default in CI3. So if you use *offset*, you have to convert *page* to *offset*.

```php
$offset = max(($page - 1), 0) * $per_page;
```

3. CI4 gets the base URL automatically from the current URL. You can't set it by config.

## Views

### Copy View files

1. Copy `application/views/*` to `app/Views/*`.

## Helpers

### Copy Helper files

1. Copy `application/helper/*` to `app/Helpers/*`.

### URL Helper

#### redirect()

1. CI4 changed `redirect()` API.
- See <https://codeigniter4.github.io/CodeIgniter4/general/common_functions.html#redirect>.
- Replace `redirect($uri)` with `return redirect()->to($uri)`, when you can return Response object.
- Replace it with`throw new \CodeIgniter\Router\Exceptions\RedirectException($uri)`, when you cannot return Response object.
- Or you could use `redirect_()` that *ci3-to-4-upgrade-helper* provides after `$this->load->helper('url')`.

#### base_url()

1. Up to version 4.3.1, CI4 `base_url()` removed the trailing slash. But the bug was fixed in v4.3.2.

#### url_title()

1. CI4's `url_title()` does not support the second param's `'dash'` and `'underscore'`. Replace them with `'-'` or `'_'`.

## Common Functions

### show_error()

1. CI4 does not have `show_error()`
- `show_error()` that *ci3-to-4-upgrade-helper* provides does not support the third argument `$heading`.
- If you want to show error page like CI3, you have to create error templates like `app/Views/errors/html/error_500.php` where `500` is the status code.
- In error templates, you can use `$message` which has the Exception message.

## Language files

1. Copy language folders (`application/language/*`) to `app/Language/`.

## Writable Paths

1. CI4 has new `writable` directory and the constant `WRITEPATH`. Adjust the paths when you write files.
