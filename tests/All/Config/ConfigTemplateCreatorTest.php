<?php declare(strict_types=1);

use Tests\All\AbstractUnitTestCase;
use NamespaceProtector\Common\PathInterface;
use NamespaceProtector\Common\FileSystemPath;
use NamespaceProtector\Config\ConfigTemplateCreator;

class ConfigTemplateCreatorTest extends AbstractUnitTestCase
{
    /** @test */
    public function it_create_json_template_work(): void
    {
        $this->markTestSkipped();
        // $path = $this->getDirectory();
        // $configTemplateCreator = new ConfigTemplateCreator('namespace-protector-config');
        // $configTemplateCreator->create($path);

        // $bool = \file_exists($path->get() . DIRECTORY_SEPARATOR . 'namespace-protector-config.json');
        // $this->assertTrue($bool);
    }

    /** @test */
    public function it_create_json_template_if_exist_create_backup(): void
    {
        $this->markTestSkipped();
        // $path = $this->getDirectory();
        // ConfigTemplateCreator::createJsonTemplateConfig($path);

        // $bool = \file_exists($path->get() . DIRECTORY_SEPARATOR . 'namespace-protector-config.json_backup.json');
        // $this->assertTrue($bool);
    }

    private function getDirectory(): PathInterface
    {
        $fileSystem = $this->StartBuildFileSystem()
            ->addFile('ast.json', 'json', 'files')
            ->addFile('namespace-protector-config', 'json', 'files')
            ->buildFileSystemUrl();

        return new FileSystemPath($fileSystem . '/files/');
    }
}
