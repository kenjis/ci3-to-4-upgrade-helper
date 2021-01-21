# CodeIgniter 3 to 4 Migration Helper

This project helps to migrate CodeIgniter3 apps to CodeIgniter4.

- The goal is to reduce migration costs.
- It provides compatible interfaces for common use cases in CodeIgniter3 apps.
- It does not aim to be 100% compatible.
- **This project is under early development.**
  - We welcome Pull Requests!

## Requirements

- CodeIgniter 4.0.5-dev or later
- PHP 7.2 or later

## Sample Code

If you use ci3-to-4-migration-helper, You can run the following code on CodeIgniter4.

*app/Controllers/News.php*
```php
<?php
namespace App\Controllers;

use App\Models\News_model;
use Kenjis\CI3Compatible\Core\CI_Controller;
use Kenjis\CI3Compatible\Library\CI_Form_validation;

/**
 * @property News_model $news_model
 * @property CI_Form_validation $form_validation
 */
class News extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('news_model');
        $this->load->helper('url_helper');
    }

    public function index()
    {
        $data['news']  = $this->news_model->get_news();
        $data['title'] = 'News archive';

        $this->load->view('templates/header', $data);
        $this->load->view('news/index', $data);
        $this->load->view('templates/footer');
    }

    public function view($slug = null)
    {
        $data['news_item'] = $this->news_model->get_news($slug);

        if (empty($data['news_item'])) {
            show_404();
        }

        $data['title'] = $data['news_item']['title'];

        $this->load->view('templates/header', $data);
        $this->load->view('news/view', $data);
        $this->load->view('templates/footer');
    }

    public function create()
    {
        $this->load->helper('form');
        $this->load->library('form_validation');

        $data['title'] = 'Create a news item';

        $this->form_validation->set_rules('title', 'Title', 'required');
        $this->form_validation->set_rules('text', 'Text', 'required');

        if ($this->form_validation->run() === false) {
            $this->load->view('templates/header', $data);
            $this->load->view('news/create');
            $this->load->view('templates/footer');
        } else {
            $this->news_model->set_news();
            $this->load->view('news/success');
        }
    }
}
```

*app/Models/News_model.php*
```php
<?php
namespace App\Models;

use Kenjis\CI3Compatible\Core\CI_Input;
use Kenjis\CI3Compatible\Core\CI_Loader;
use Kenjis\CI3Compatible\Core\CI_Model;
use Kenjis\CI3Compatible\Database\CI_DB;

/**
 * @property CI_Loader $load
 * @property CI_DB $db
 * @property CI_Input $input
 */
class News_model extends CI_Model
{
    public function __construct()
    {
        $this->load->database();
    }

    public function get_news($slug = false)
    {
        if ($slug === false) {
            $query = $this->db->get('news');
            return $query->result_array();
        }

        $query = $this->db->get_where('news', ['slug' => $slug]);

        return $query->row_array();
    }

    public function set_news()
    {
        $this->load->helper('url');

        $slug = url_title($this->input->post('title'), 'dash', true);

        $data = [
            'title' => $this->input->post('title'),
            'slug'  => $slug,
            'text'  => $this->input->post('text')
        ];

        return $this->db->insert('news', $data);
    }
}
```

*app/Views/news/create.php*
```php
<h2><?php echo $title; ?></h2>

<?php echo validation_errors(); ?>

<?php echo form_open('news/create'); ?>

    <label for="title">Title</label>
    <input type="input" name="title" /><br />

    <label for="text">Text</label>
    <textarea name="text"></textarea><br />

    <input type="submit" name="submit" value="Create news item" />

</form>
```

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

#### Pagination

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
