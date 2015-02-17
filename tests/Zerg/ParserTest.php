<?php

namespace Zerg;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    public function testParse()
    {
        $stream = new Stream\StringStream('123abcdefgqwertyahnytjssdadfkjhb');

        $schemaArr = [
            'a' => ['int', 8],
            'b' => ['string', 2],
            'c' => ['enum', 8, [
                    'values' => [
                        0 => 'A',
                        1 => 'B',
                        2 => 'C',
                        3 => 'D',
                        97 => 'right'
                    ]
                ]
            ],
            'd' => ['raw', 16],
            ['padding', 4 * 8],
            'e' => [
                'e.1' => ['int', 8],
                ['padding', 64],
                'e.2' => ['string', 5]
            ],
            'f' => ['conditional', '/e/e.2', [
                    'schemas' => [
                        'integ' => ['int', 16],
                        'strin' => ['string', 2],
                        'ytjss' => ['enum', 8, [
                                'values' => [
                                    3 => 'wrong',
                                    4 => 'wrong'
                                ],
                                'default' => 'right'
                            ]
                        ],
                    ],
                    'default' => ['string', 5]
                ]
            ]
        ];

        $parser = new Parser($stream);
        $schemaObj = new Schema($schemaArr);
        $result = $parser->parse($schemaObj);

        $this->assertInstanceOf('\\Zerg\\DataSet', $result);

        $result = $result->getData();

        $this->assertSame(49, $result['a']);
        $this->assertSame('23', $result['b']);
        $this->assertSame('right', $result['c']);
        $this->assertSame('bc', $result['d']);
        $this->assertArrayHasKey('e.1', $result['e']);
        $this->assertSame(113, $result['e']['e.1']);
        $this->assertSame('ytjss', $result['e']['e.2']);
        $this->assertSame('right', $result['f']);

    }

} 