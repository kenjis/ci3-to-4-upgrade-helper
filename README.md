# CodeIgniter 3 to 4 Upgrade Helper

This project helps you upgrade your CodeIgniter3 apps to CodeIgniter4.

- The goal is to reduce upgrade costs.
- It provides compatible interfaces for common use cases in CodeIgniter3 apps.
- It does not aim to be 100% compatible.
- **This project is under early development.**
- **This project is under early development.**
- **This project is under early development.**
  - We welcome Pull Requests!

## Requirements

- CodeIgniter 4.1.0 or later
  - [ci4-app-template](https://github.com/kenjis/ci4-app-template) can be used
- PHP 7.3 or later

## Sample Code

- https://github.com/kenjis/ci3-to-4-news
- https://github.com/kenjis/ci4-online-games-store
- https://github.com/kenjis/ci4-qrcode

If you use *ci3-to-4-upgrade-helper*, You can run the following code on CodeIgniter4.

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

use Kenjis\CI3Compatible\Core\CI_Model;

class News_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();

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

        $slug = url_title($this->input->post('title'), '-', true);

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

## How to Upgrade from CI3 to CI4

See [How to Upgrade from CI3 to CI4](docs/HowToUpgradeFromCI3ToCI4.md).

## For Development

### Installation

    composer install

### Available Commands

    composer test              // Run unit test
    composer tests             // Test and quality checks
    composer cs-fix            // Fix the coding style
    composer sa                // Run static analysys tools
    composer run-script --list // List all commands
