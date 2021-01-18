# CodeIgniter 3 to 4 Migration Helper

This project helps to migrate CodeIgniter3 apps to CodeIgniter4.

- The goal is to reduce migration costs.
- It provides compatible interfaces for common use cases in CodeIgniter3 apps.
- It does not aim to be 100% compatible.
- **This project is under early development.**
  - We welcome Pull Requests!

## Requirements

- CodeIgniter 4.0.5-dev or later

## How to Migrate from CI3 to CI4

### Install CodeIgniter4

See <https://codeigniter4.github.io/CodeIgniter4/installation/index.html>.

**Note:** CodeIgniter 4.0.4 is not supported. Use 4.0.5-dev (`develop` branch) or later.

### Install ci3-to-4-migration-helper

```
$ composer require kenjis/ci3-to-4-migration-helper:1.x-dev
```

### Config

#### config.php

1. Migrate `application/config/config.php` to `app/Config/App.php` manually. You can set your own values like base_url with `.env` file.

#### Routing

1. Migrate `application/config/routes.php` to `app/Config/Routes.php` manually.

See <https://codeigniter4.github.io/CodeIgniter4/incoming/routing.html#setting-your-own-routing-rules>.

#### Custom Config Files

1. Convert custom config files to Config classes manually.

See <https://codeigniter4.github.io/CodeIgniter4/general/configuration.html#creating-configuration-files>.

#### Other Config Files

1. Migrate remaining `application/config/*.php` to `app/Config/*.php` manually. You can set your own values like database password with `.env` file.

#### app/Config/View.php

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

### Hooks

1. Migrate `application/config/hooks.php` to `app/Config/Events.php` or `app/Config/Filters.php` manually.
2. Migrate `application/hooks/*` to *Events* or *Controller Filters* manually.
   
See <https://codeigniter4.github.io/CodeIgniter4/extending/events.html> or <https://codeigniter4.github.io/CodeIgniter4/incoming/filters.html>.

### Controllers

#### Copy Controller files

1. Copy `application/controllers/*` to `app/Controllers/*`.
2. Rename the sub-folder names so that only the first letter is uppercase.

#### Add Namespace and Use statement

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

### Models

#### Copy Model files

1. Copy `application/models/*` to `app/Models/*`.
2. Rename the sub-folder names so that only the first letter is uppercase.

#### Add Namespace and Use statement

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

### Libraries

#### Copy Library files

1. Copy `application/libraries/*` to `app/Libraries/*`.
2. Rename the sub-folder names so that only the first letter is uppercase.

#### Add Namespace

1. Add `namespace App\Libraries;`.

Example:
```php
namespace App\Libraries; // Add

class Seeder
{
   ...
}
```

### Views

#### Copy View files

1. Copy `application/views/*` to `app/Views/*`.

### Helpers

#### Copy Helper files

1. Copy `application/helper/*` to `app/Helpers/*`.

## For Development

### Installation

    composer install

### Available Commands

    composer test              // Run unit test
    composer tests             // Test and quality checks
    composer cs-fix            // Fix the coding style
    composer sa                // Run static analysys tools
    composer run-script --list // List all commands
