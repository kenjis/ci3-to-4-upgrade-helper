<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Library;

use CodeIgniter\Pager\Pager;
use Config\Pager as PagerConfig;
use Config\Services;

use function is_array;

class CI_Pagination
{
    /** @var Pager */
    private $pager;

    /** @var array */
    private $ci3Config;

    /**
     * Constructor
     *
     * @param   array|PagerConfig|null $params Initialization parameters
     *
     * @return  void
     */
    public function __construct($params = null)
    {
        helper('url');

        if (is_array($params)) {
            $this->initialize($params);

            return;
        }

        $this->pager = Services::pager($params);
    }

    /**
     * Initialize Preferences
     *
     * @param   array $params Initialization parameters
     *
     * @return  CI_Pagination
     */
    public function initialize(array $params = []): self
    {
        $this->ci3Config = $params;

        $config = new PagerConfig();
        $config->perPage = $params['per_page'];

        $this->pager = Services::pager($config);

        return $this;
    }

    /**
     * Generate the pagination links
     *
     * @return  string
     */
    public function create_links(): string
    {
        return $this->pager->makeLinks(
            $this->pager->getCurrentPage(),
            $this->ci3Config['per_page'],
            $this->ci3Config['total_rows']
        );
    }
}
