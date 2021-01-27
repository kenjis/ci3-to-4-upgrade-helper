<?php

declare(strict_types=1);

namespace Kenjis\CI3Compatible\Library;

use Kenjis\CI3Compatible\TestCase;

class CI_ParserTest extends TestCase
{
    public function test_parse_string(): void
    {
        $parser = new CI_Parser();

        $template = 'Hello, {firstname} {lastname}';
        $data = [
            'title' => 'Mr',
            'firstname' => 'John',
            'lastname' => 'Doe',
        ];
        $output = $parser->parse_string($template, $data, true);

        $expected = 'Hello, John Doe';
        $this->assertEquals($expected, $output);
    }

    public function test_parse_string_variable_pair(): void
    {
        $parser = new CI_Parser();

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
        $output = $parser->parse_string($template, $data, true);

        $expected = 'Hello, John Doe (Mr Mr )';
        $this->assertEquals($expected, $output);
    }

    public function test_parse()
    {
        $viewPath = __DIR__ . '/../../App/Views';
        $parser = new CI_Parser($viewPath);

        $data = [
            'blog_title' => 'My Blog Title',
            'blog_heading' => 'My Blog Heading',
            'blog_entries' => [
                ['title' => 'Title 1', 'body' => 'Body 1'],
                ['title' => 'Title 2', 'body' => 'Body 2'],
            ],
        ];
        $output = $parser->parse('parser/blog_template', $data, true);

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
}
