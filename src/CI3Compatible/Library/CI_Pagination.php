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

    /** @var PagerConfig */
    private $pagerConfig;

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

        $this->pagerConfig = new PagerConfig();

        if (is_array($params)) {
            $this->initialize($params);

            return;
        }

        if ($params instanceof PagerConfig) {
            $this->pagerConfig = $params;
        }

        $this->pager = Services::pager($this->pagerConfig);
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
        $this->pagerConfig->perPage = $params['per_page'];

        foreach ($params as $property => $value) {
            $this->pagerConfig->$property = $value;
        }

        $this->pager = Services::pager($this->pagerConfig);

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
            $this->pagerConfig->perPage,
            $this->pagerConfig->total_rows
        );
    }
}
