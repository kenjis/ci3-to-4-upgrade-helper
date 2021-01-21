<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Library;

use CodeIgniter\Pager\Pager;
use Config\Pager as PagerConfig;
use Config\Services;
use Kenjis\CI3Compatible\Exception\NotSupportedException;

use function implode;
use function in_array;
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

        $this->checkUnsupportedConfigs($params);

        foreach ($params as $property => $value) {
            $this->pagerConfig->$property = $value;
        }

        $this->pager = Services::pager($this->pagerConfig);

        return $this;
    }

    private function checkUnsupportedConfigs(array $params): void
    {
        $customizingLinkConfigs = [
            'full_tag_open',
            'full_tag_close',
            'first_link',
            'first_tag_open',
            'first_tag_close',
            'first_url',
            'last_link',
            'last_tag_open',
            'last_tag_close',
            'next_link',
            'next_tag_open',
            'next_tag_close',
            'prev_link',
            'prev_tag_open',
            'prev_tag_close',
            'cur_tag_open',
            'cur_tag_close',
            'num_tag_open',
            'num_tag_close',
            'display_pages',
            'attributes',
        ];

        $unsupportedConfigs = [];

        foreach ($params as $property => $value) {
            if (in_array($property, $customizingLinkConfigs, true)) {
                $unsupportedConfigs[] = $property;
            }
        }

        if ($unsupportedConfigs !== []) {
            throw new NotSupportedException(
                'You can not customize Pagination Link by config '
                . implode(', ', $unsupportedConfigs) . '.'
                . ' Create your own templates, and configure to use them.'
                . ' See <https://github.com/kenjis/ci3-to-4-migration-helper#pagination>.'
            );
        }
    }

    /**
     * Generate the pagination links
     *
     * @return  string
     */
    public function create_links(): string
    {
        $this->pager->setSegment($this->pagerConfig->uri_segment ?? 3);

        return $this->pager->makeLinks(
            $this->pager->getCurrentPage(),
            $this->pagerConfig->perPage,
            $this->pagerConfig->total_rows
        );
    }
}
