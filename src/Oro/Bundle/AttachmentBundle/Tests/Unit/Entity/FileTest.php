<?php

namespace Oro\Bundle\AttachmentBundle\Tests\Unit\Entity;

use Oro\Bundle\AttachmentBundle\Entity\File;
use Oro\Bundle\UserBundle\Entity\User;
use Oro\Component\Testing\Unit\EntityTestCaseTrait;
use Oro\Component\Testing\Unit\EntityTrait;
use Symfony\Component\HttpFoundation\File\File as ComponentFile;

class FileTest extends \PHPUnit\Framework\TestCase
{
    use EntityTestCaseTrait;
    use EntityTrait;

    /** @var File */
    private $entity;

    protected function setUp(): void
    {
        $this->entity = new File();
    }

    public function testAccessors(): void
    {
        $properties = [
            ['id', 1],
            ['uuid', '123e4567-e89b-12d3-a456-426655440000', false],
            ['owner', new User()],
            ['filename', 'sample_filename'],
            ['extension', 'smplext'],
            ['mimeType', 'sample/mime-type'],
            ['originalFilename', 'sample_original_filename'],
            ['fileSize', 12345],
            ['parentEntityClass', \stdClass::class],
            ['parentEntityId', 2],
            ['parentEntityFieldName', 'sampleFieldName'],
            ['createdAt', new \DateTime('today')],
            ['updatedAt', new \DateTime('today')],
            ['file', new ComponentFile('sample/file', false)],
            ['emptyFile', true],

        ];

        self::assertPropertyAccessors($this->entity, $properties);
    }

    public function testPrePersists(): void
    {
        $testDate = new \DateTime('now', new \DateTimeZone('UTC'));

        $this->entity->prePersist();
        $this->entity->preUpdate();

        $this->assertEquals($testDate->format('Y-m-d'), $this->entity->getCreatedAt()->format('Y-m-d'));
        $this->assertEquals($testDate->format('Y-m-d'), $this->entity->getUpdatedAt()->format('Y-m-d'));
    }

    public function testEmptyFile(): void
    {
        $this->assertNull($this->entity->isEmptyFile());
        $this->entity->setEmptyFile(true);
        $this->assertTrue($this->entity->isEmptyFile());
    }

    public function testToString(): void
    {
        $this->entity->setFilename('file.doc');
        $this->assertEquals('file.doc', $this->entity->__toString());

        $this->entity->setOriginalFilename('original.doc');
        $this->assertEquals('file.doc (original.doc)', $this->entity->__toString());
    }

    public function testSerialize(): void
    {
        $this->assertSame(serialize([null, null, $this->entity->getUuid()]), $this->entity->serialize());

        $this->assertEquals(
            serialize([1, 'sample_filename', 'test-uuid']),
            $this->getEntity(
                File::class,
                ['id' => 1, 'filename' => 'sample_filename', 'uuid' => 'test-uuid']
            )->serialize()
        );
    }

    public function testUnserialize(): void
    {
        $this->entity->unserialize(serialize([1, 'sample_filename', 'test-uuid']));

        $this->assertSame('sample_filename', $this->entity->getFilename());
        $this->assertSame(1, $this->entity->getId());
        $this->assertSame('test-uuid', $this->entity->getUuid());
    }

    public function testClone(): void
    {
        /** @var File $file */
        $file = $this->getEntity(
            File::class,
            [
                'id' => 1,
                'uuid' => 'sample-uuid',
                'parentEntityClass' => \stdClass::class,
                'parentEntityFieldName' => 'sampleField',
                'parentEntityId' => 1,
            ]
        );

        $clonedFile = clone $file;

        $this->assertNull($clonedFile->getId());
        $this->assertNull($clonedFile->getUuid());
        $this->assertNull($clonedFile->getParentEntityClass());
        $this->assertNull($clonedFile->getParentEntityFieldName());
        $this->assertNull($clonedFile->getParentEntityId());
    }
}
