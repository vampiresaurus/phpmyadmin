<?php

declare(strict_types=1);

namespace PhpMyAdmin\Tests\Controllers;

use PhpMyAdmin\Controllers\GisDataEditorController;
use PhpMyAdmin\DatabaseInterface;
use PhpMyAdmin\Template;
use PhpMyAdmin\Tests\AbstractTestCase;
use PhpMyAdmin\Tests\Stubs\ResponseRenderer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;

#[CoversClass(GisDataEditorController::class)]
class GisDataEditorControllerTest extends AbstractTestCase
{
    private GisDataEditorController|null $controller = null;

    protected function setUp(): void
    {
        parent::setUp();

        $GLOBALS['server'] = 1;
        $GLOBALS['text_dir'] = 'ltr';
        $GLOBALS['PMA_PHP_SELF'] = 'index.php';
        $GLOBALS['db'] = 'db';
        $GLOBALS['table'] = 'table';

        DatabaseInterface::$instance = $this->createDatabaseInterface();

        $this->controller = new GisDataEditorController(new ResponseRenderer(), new Template());
    }

    #[DataProvider('providerForTestValidateGisData')]
    #[Group('gis')]
    public function testValidateGisData(mixed $gisData, string $type, string|null $value, string $expected): void
    {
        /** @var string $gisData */
        $gisData = $this->callFunction(
            $this->controller,
            GisDataEditorController::class,
            'extractGisType',
            [
                $gisData,
                $type,
                $value,
            ],
        );
        $this->assertEquals($expected, $gisData);
    }

    /** @return iterable<array{0:mixed,1:string,2:string|null,3:string}> */
    public static function providerForTestValidateGisData(): iterable
    {
        yield [
            null,
            'GEOMETRY',
            'GEOMETRYCOLLECTION()',
            'GEOMETRYCOLLECTION',
        ];

        yield [
            null,
            'GEOMETRY',
            'GEOMETRYCOLLECTION EMPTY',
            'GEOMETRYCOLLECTION',
        ];
    }
}
