<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Library;

use CodeIgniter\Email\Email;
use Config\Email as EmailConfig;
use Config\Services;

class CI_Email
{
    /** @var Email */
    private $email;
    private $propertyMap = [
        // CI3 => CI4
        'useragent' => 'userAgent',
        'mailpath' => 'mailPath',
        'protocol' => 'protocol',
        'smtp_host' => 'SMTPHost',
        'smtp_user' => 'SMTPUser',
        'smtp_pass' => 'SMTPPass',
        'smtp_port' => 'SMTPPort',
        'smtp_timeout' => 'SMTPTimeout',
        'smtp_keepalive' => 'SMTPKeepAlive',
        'smtp_crypto' => 'SMTPCrypto',
        'wordwrap' => 'wordWrap',
        'wrapchars' => 'wrapChars',
        'mailtype' => 'mailType',
        'charset' => 'charset',
        'alt_message' => 'altMessage',
        'validate' => 'validate',
        'priority' => 'priority',
        'newline' => 'newline',
        'crlf' => 'CRLF',
        'dsn' => 'DSN',
        'send_multipart' => 'sendMultipart',
        'bcc_batch_mode' => 'BCCBatchMode',
        'bcc_batch_size' => 'BCCBatchSize',
    ];

    /**
     * @param EmailConfig|array|null $config
     */
    public function __construct($config = null)
    {
        $this->email = Services::email($config);
    }

    /**
     * For debugging
     *
     * @return Email
     */
    public function getCI4Library(): Email
    {
        return $this->email;
    }

    /**
     * Initialize preferences
     *
     * @param   array $config
     *
     * @return  CI_Email
     */
    public function initialize(array $config = []): self
    {
        $config = $this->convertToCI4Config($config);
        $this->email->initialize($config);

        return $this;
    }

    private function convertToCI4Config(array $config): array
    {
        foreach ($config as $key => $value) {
            if ($this->propertyMap[$key]) {
                $key = $this->propertyMap[$key];
            }

            $newConfig[$key] = $value;
        }

        return $newConfig;
    }
}
