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
}
