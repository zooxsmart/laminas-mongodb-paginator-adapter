<?php declare(strict_types=1);

namespace MariojrrcTest\Laminas\Paginator\Hook;

use DG\BypassFinals;
use PHPUnit\Runner\BeforeTestHook;

// https://www.tomasvotruba.com/blog/2019/03/28/how-to-mock-final-classes-in-phpunit/
final class BypassFinalHook implements BeforeTestHook
{
    public function executeBeforeTest(string $test): void
    {
        BypassFinals::enable();
    }
}
