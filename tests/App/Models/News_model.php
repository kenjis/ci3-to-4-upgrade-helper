<?php

declare(strict_types=1);

namespace App\Models;

use Kenjis\CI3Compatible\Core\CI_Model;

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
            'text'  => $this->input->post('text'),
        ];

        return $this->db->insert('news', $data);
    }
}
