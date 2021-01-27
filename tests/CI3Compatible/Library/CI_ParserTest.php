<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Library;

use Kenjis\CI3Compatible\TestCase;

class CI_ParserTest extends TestCase
{
    /** @var CI_Parser */
    private $parser;

    public function setUp(): void
    {
        $viewPath = __DIR__ . '/../../App/Views';
        $this->parser = new CI_Parser($viewPath);
    }

    public function test_parse_string_return(): void
    {
        $template = 'Hello, {firstname} {lastname}';
        $data = [
            'title' => 'Mr',
            'firstname' => 'John',
            'lastname' => 'Doe',
        ];
        $output = $this->parser->parse_string($template, $data, true);

        $expected = 'Hello, John Doe';
        $this->assertEquals($expected, $output);
    }

    public function test_parse_string_not_return(): void
    {
        $expected = 'Hello, John Doe';
        $this->expectOutputString($expected);

        $template = 'Hello, {firstname} {lastname}';
        $data = [
            'title' => 'Mr',
            'firstname' => 'John',
            'lastname' => 'Doe',
        ];
        $this->parser->parse_string($template, $data);
    }

    public function test_parse_string_variable_pair(): void
    {
        $template = 'Hello, {firstname} {lastname} ({degrees}{degree} {/degrees})';
        $data = [
            'degree' => 'Mr',
            'firstname' => 'John',
            'lastname' => 'Doe',
            'degrees' => [
                ['degree' => 'BSc'],
                ['degree' => 'PhD'],
            ],
        ];
        $output = $this->parser->parse_string($template, $data, true);

        $expected = 'Hello, John Doe (Mr Mr )';
        $this->assertEquals($expected, $output);
    }

    public function test_parse_return()
    {
        $data = [
            'blog_title' => 'My Blog Title',
            'blog_heading' => 'My Blog Heading',
            'blog_entries' => [
                ['title' => 'Title 1', 'body' => 'Body 1'],
                ['title' => 'Title 2', 'body' => 'Body 2'],
            ],
        ];
        $output = $this->parser->parse('parser/blog_template', $data, true);

        $expected = '<html>
<head>
    <title>My Blog Title</title>
</head>
<body>
<h3>My Blog Heading</h3>


<h5>Title 1</h5>
<p>Body 1</p>

<h5>Title 2</h5>
<p>Body 2</p>


</body>
</html>
';
        $this->assertEquals($expected, $output);
    }

    public function test_parse_not_return()
    {
        $expected = '<html>
<head>
    <title>My Blog Title</title>
</head>
<body>
<h3>My Blog Heading</h3>


<h5>Title 1</h5>
<p>Body 1</p>

<h5>Title 2</h5>
<p>Body 2</p>


</body>
</html>
';
        $this->expectOutputString($expected);

        $data = [
            'blog_title' => 'My Blog Title',
            'blog_heading' => 'My Blog Heading',
            'blog_entries' => [
                ['title' => 'Title 1', 'body' => 'Body 1'],
                ['title' => 'Title 2', 'body' => 'Body 2'],
            ],
        ];
        $this->parser->parse('parser/blog_template', $data);
    }

    public function test_set_delimiters(): void
    {
        $this->parser->set_delimiters('{{', '}}');

        $template = 'Hello, {{firstname}} {lastname}';
        $data = [
            'title' => 'Mr',
            'firstname' => 'John',
            'lastname' => 'Doe',
        ];
        $output = $this->parser->parse_string($template, $data, true);

        $expected = 'Hello, John {lastname}';
        $this->assertEquals($expected, $output);
    }
}
