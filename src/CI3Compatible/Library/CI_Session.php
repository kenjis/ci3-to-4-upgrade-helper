<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Library;

use CodeIgniter\Session\Session;
use Config\App;
use Config\Services;
use Kenjis\CI3Compatible\Exception\NotSupportedException;

use function is_array;
use function session_destroy;

class CI_Session
{
    /** @var Session */
    private $session;

    /**
     * @param   App|array|null $params Configuration parameters
     *
     * @return  void
     */
    public function __construct($params = null)
    {
        if (is_array($params)) {
            throw new NotSupportedException(
                'Configuration array is not supported.'
                . ' Please convert it to `Config\App` class.'
                . ' See <https://codeigniter4.github.io/CodeIgniter4/libraries/sessions.html#session-preferences>.'
            );
        }

        $this->session = Services::session($params);
    }

    /**
     * For debugging
     *
     * @return Session
     *
     * @internal
     */
    public function getCI4Library(): Session
    {
        return $this->session;
    }

    /**
     * Set userdata
     *
     * Legacy CI_Session compatibility method
     *
     * @param   mixed $data  Session data key or an associative array
     * @param   mixed $value Value to store
     *
     * @return  void
     */
    public function set_userdata($data, $value = null)
    {
        $this->session->set($data, $value);
    }

    /**
     * Userdata (fetch)
     *
     * Legacy CI_Session compatibility method
     *
     * @param   string $key Session data key
     *
     * @return  mixed   Session data value or NULL if not found
     */
    public function userdata(?string $key = null)
    {
        return $this->session->get($key);
    }

    /**
     * __set()
     *
     * @param   string $key   Session data key
     * @param   mixed  $value Session data value
     *
     * @return  void
     */
    public function __set(string $key, $value)
    {
        $this->session->set($key, $value);
    }

    /**
     * __get()
     *
     * @param   string $key 'session_id' or a session data key
     *
     * @return  mixed
     */
    public function __get(string $key)
    {
        return $this->session->get($key);
    }

    /**
     * Unset userdata
     *
     * Legacy CI_Session compatibility method
     *
     * @param   mixed $key Session data key(s)
     *
     * @return  void
     */
    public function unset_userdata($key)
    {
        $this->session->remove($key);
    }

    /**
     * Set flashdata
     *
     * Legacy CI_Session compatibility method
     *
     * @param   mixed $data  Session data key or an associative array
     * @param   mixed $value Value to store
     *
     * @return  void
     */
    public function set_flashdata($data, $value = null)
    {
        $this->session->setFlashdata($data, $value);
    }

    /**
     * Flashdata (fetch)
     *
     * Legacy CI_Session compatibility method
     *
     * @param   string $key Session data key
     *
     * @return  mixed   Session data value or NULL if not found
     */
    public function flashdata(?string $key = null)
    {
        return $this->session->getFlashdata($key);
    }

    /**
     * Session destroy
     *
     * Legacy CI_Session compatibility method
     *
     * @return  void
     */
    public function sess_destroy(): void
    {
        session_destroy();
    }
}
