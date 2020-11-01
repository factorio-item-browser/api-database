<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Entity;

use BluePsyduck\TestHelper\ReflectionTrait;
use Doctrine\Common\Collections\ArrayCollection;
use FactorioItemBrowser\Api\Database\Entity\IconImage;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;
use ReflectionException;

/**
 * The PHPUnit test of the IconImage class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Database\Entity\IconImage
 */
class IconImageTest extends TestCase
{
    use ReflectionTrait;

    /**
     * Tests the constructing.
     * @covers ::__construct
     * @covers ::getIcons
     */
    public function testConstruct(): void
    {
        $image = new IconImage();

        $this->assertInstanceOf(ArrayCollection::class, $image->getIcons());
    }

    /**
     * Tests the setting and getting the id.
     * @covers ::getId
     * @covers ::setId
     */
    public function testSetAndGetId(): void
    {
        /* @var UuidInterface&MockObject $id */
        $id = $this->createMock(UuidInterface::class);
        $image = new IconImage();

        $this->assertSame($image, $image->setId($id));
        $this->assertSame($id, $image->getId());
    }

    /**
     * Tests the setting and getting the contents.
     * @covers ::getContents
     * @covers ::setContents
     */
    public function testSetAndGetContents(): void
    {
        $contents = 'abc';
        $image = new IconImage();

        $this->assertSame($image, $image->setContents($contents));
        $this->assertSame($contents, $image->getContents());
    }

    /**
     * Tests the getting the contents.
     * @throws ReflectionException
     * @covers ::getContents
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

        $image = new IconImage();

        $this->injectProperty($image, 'contents', $stream);
        $this->assertSame($contents, $image->getContents());
    }

    /**
     * Tests the setting and getting the size.
     * @covers ::getSize
     * @covers ::setSize
     */
    public function testSetAndGetSize(): void
    {
        $size = 42;
        $image = new IconImage();

        $this->assertSame($image, $image->setSize($size));
        $this->assertSame($size, $image->getSize());
    }
}
