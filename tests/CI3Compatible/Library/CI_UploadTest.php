<?php

declare(strict_types=1);

namespace CodeIgniter\HTTP\Files;

use Kenjis\CI3Compatible\Library\CI_Upload;
use Kenjis\CI3Compatible\TestCase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

use function file_exists;
use function file_put_contents;
use function is_dir;
use function mkdir;
use function realpath;
use function rmdir;

class CI_UploadTest extends TestCase
{
    /** @var vfsStreamDirectory */
    private $root;

    /** @var string */
    private $start;

    /** @var string */
    private $destination;

    public function setUp(): void
    {
        $this->root = vfsStream::setup();
        vfsStream::copyFromFileSystem(
            __DIR__ . '/../../fixture',
            $this->root
        );

        $this->start = $this->root->url();

        $this->destination = $this->start . '/destination';
        if (is_dir($this->destination)) {
            rmdir($this->destination);
        }

        $_FILES = [];
    }

    public function test_create_instance(): void
    {
        $config = [];
        $upload = new CI_Upload($config);

        $this->assertInstanceOf(CI_Upload::class, $upload);
    }

    public function test_do_upload(): void
    {
        $filename = 'pexels-skully-mba-1316484.jpg';
        $_FILES = [
            'userfile1' => [
                'name'     => $filename,
                'type'     => 'image/jpeg',
                'size'     => 7755,
                'tmp_name' => $this->start . '/images/' . $filename,
                'error'    => 0,
            ],
        ];

        $destination = $this->destination;
        is_dir($destination) || mkdir($destination, 0777, true);

        $config = [
            'upload_path'     => $destination,
            'encrypt_name'    => true,
            'allowed_types'   => 'jpg|jpeg|png|JPG|PNG|JPEG',
            'max_size'        => 3000,
            'max_width'       => 0,
            'max_height'      => 0,
//            'overwrite'       => true,
//            'file_ext_tolower' => true,
        ];
        $upload = new CI_Upload($config);

        $ret = $upload->do_upload('userfile1');

        $this->assertTrue($ret);
    }

    public function test_data_image_file(): void
    {
        $filename = 'pexels-skully-mba-1316484';
        $_FILES = [
            'userfile1' => [
                'name'     => $filename . '.jpg',
                'type'     => 'image/jpeg',
                'size'     => 7755,
                'tmp_name' => $this->start . '/images/' . $filename . '.jpg',
                'error'    => 0,
            ],
        ];
        $collection = new FileCollection();
        $file = $collection->getFile('userfile1');
        $this->setPrivateProperty($file, 'hasMoved', true);

        $config = [
            'upload_path'     => __DIR__ . '/../../fixture/images/',
            'encrypt_name'    => true,
            'allowed_types'   => 'jpg|jpeg|png|JPG|PNG|JPEG',
            'max_size'        => 3000,
            'max_width'       => 0,
            'max_height'      => 0,
//            'overwrite'       => true,
//            'file_ext_tolower' => true,
        ];
        $upload = new CI_Upload($config);
        $this->setPrivateProperty($upload, 'file', $file);

        $data = $upload->data();

        $expected = [
            'file_name' => $filename . '.jpg',
            'file_type' => 'image/jpeg',
            'file_path' => realpath(__DIR__ . '/../../fixture/images/'),
            'full_path' => realpath(__DIR__ . '/../../fixture/images/' . $filename . '.jpg'),
            'raw_name' => $filename,
            'orig_name' => $filename . '.jpg',
            'client_name' => $filename . '.jpg',
            'file_ext' => '.jpg',
            'file_size' => 7.57,
            'is_image' => true,
            'image_width' => 213,
            'image_height' => 320,
            'image_type' => 'jpeg',
            'image_size_str' => 'width="213" height="320"',
        ];
        $this->assertSame($expected, $data);
    }

    public function test_data_text_file(): void
    {
        $filename = 'test';
        $_FILES = [
            'userfile1' => [
                'name'     => $filename . '.txt',
                'type'     => 'text/plain',
                'size'     => 21,
                'tmp_name' => '/tmp/fileA.txt',
                'error'    => 0,
            ],
        ];
        $collection = new FileCollection();
        $file = $collection->getFile('userfile1');
        $this->setPrivateProperty($file, 'hasMoved', true);

        $config = [
            'upload_path'     => __DIR__ . '/../../fixture/files/',
            'encrypt_name'    => true,
            'allowed_types'   => 'txt',
            'max_size'        => 3000,
            'max_width'       => 0,
            'max_height'      => 0,
//            'overwrite'       => true,
//            'file_ext_tolower' => true,
        ];
        $upload = new CI_Upload($config);
        $this->setPrivateProperty($upload, 'file', $file);

        $data = $upload->data();

        $expected = [
            'file_name' => $filename . '.txt',
            'file_type' => 'text/plain',
            'file_path' => realpath(__DIR__ . '/../../fixture/files/'),
            'full_path' => realpath(__DIR__ . '/../../fixture/files/' . $filename . '.txt'),
            'raw_name' => $filename,
            'orig_name' => $filename . '.txt',
            'client_name' => $filename . '.txt',
            'file_ext' => '.txt',
            'file_size' => 0.02,
            'is_image' => false,
            'image_width' => null,
            'image_height' => null,
            'image_type' => '',
            'image_size_str' => '',
        ];
        $this->assertSame($expected, $data);
    }
}

function is_uploaded_file($filename)
{
    if (! file_exists($filename)) {
        file_put_contents($filename, 'data');
    }

    return file_exists($filename);
}
