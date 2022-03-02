<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Entity;

use BluePsyduck\TestHelper\ReflectionTrait;
use Doctrine\Common\Collections\ArrayCollection;
use FactorioItemBrowser\Api\Database\Entity\IconData;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use ReflectionException;

/**
 * The PHPUnit test of the IconImage class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Database\Entity\IconData
 */
class IconDataTest extends TestCase
{
    use ReflectionTrait;

    private function createInstance(): IconData
    {
        return new IconData();
    }

    public function testConstruct(): void
    {
        $instance = $this->createInstance();

        $this->assertInstanceOf(ArrayCollection::class, $instance->getIcons());
    }

    public function testId(): void
    {
        $value = Uuid::fromString('01234567-89ab-cdef-0123-456789abcdef');
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setId($value));
        $this->assertSame($value, $instance->getId());
    }

    public function testContents(): void
    {
        $value = 'abc';
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setContents($value));
        $this->assertSame($value, $instance->getContents());
    }

    /**
     * @throws ReflectionException
     */
    public function testGetContentsAsResource(): void
    {
        $contents = 'abc';

        $stream = fopen('php://memory', 'r+');
        if ($stream === false) {
            $this->fail('unable to open memory stream.');
        }
        fwrite($stream, $contents);
        fseek($stream, 0);

        $instance = $this->createInstance();
        $this->injectProperty($instance, 'contents', $stream);

        $this->assertSame($contents, $instance->getContents());
    }

    public function testSize(): void
    {
        $value = 42;
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setSize($value));
        $this->assertSame($value, $instance->getSize());
    }
}