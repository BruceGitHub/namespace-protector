<?php declare(strict_types=1);

use Tests\Unit\AbstractUnitTestCase;
use NamespaceProtector\Config\ConfigMaker;
use NamespaceProtector\Common\FileSystemPath;

class ConfigTest extends AbstractUnitTestCase
{
    /** @test */
    public function it_load_from_file_config_work(): void
    {
        $fileSystem = $this->StartBuildFileSystem()
            ->addFile('namespace-protector-config-full-options.json', 'json', 'json')
            ->addDirectory('stat_path_in_config')
            ->addDirectory('composer_json_path')
            ->buildFileSystem();

        $fileConfigJson = $fileSystem->url() . '/json/namespace-protector-config-full-options.json';

        $configMaker = new ConfigMaker();
        $configLoaded = $configMaker->createFromFile(new FileSystemPath($fileConfigJson));

        $this->assertEquals('MODE_MAKE_VENDOR_PRIVATE', $configLoaded->getMode());
        $this->assertEquals('vfs://root/stat_path_in_config', $configLoaded->getStartPath()->get());
        $this->assertEquals('vfs://root/composer_json_path', $configLoaded->getPathComposerJson()->get());
        $this->assertEquals('PublicEntriOne', $configLoaded->getPublicEntries()[0]);
        $this->assertEquals('PublicEntriTwo', $configLoaded->getPublicEntries()[1]);
        $this->assertEquals('PrivateEntriOne', $configLoaded->getPrivateEntries()[0]);
        $this->assertEquals('PrivateEntriTwo', $configLoaded->getPrivateEntries()[1]);
        $this->assertTrue($configLoaded->enabledCache());
    }

    /** @test */
    public function it_load_from_file_config_cache_flag_true_work(): void
    {
        $fileSystem = $this->StartBuildFileSystem()
            ->addFileWithCallable(
                'namespace-protector-config-full-options.json',
                'json',
                'json',
                function ($directoryReal, $pathFile) {
                    return $this->helperEditChangesFlag($directoryReal, $pathFile, true);
                }
            )
            ->addDirectory('stat_path_in_config')
            ->addDirectory('composer_json_path')
            ->buildFileSystem();

        $fileConfigJson = $fileSystem->url() . '/json/namespace-protector-config-full-options.json';

        $configMaker = new ConfigMaker();
        $configLoaded = $configMaker->createFromFile(new FileSystemPath($fileConfigJson));

        $this->assertEquals(true, $configLoaded->enabledCache());
    }

    /** @test */
    public function it_load_from_file_config_cache_flag_false_work(): void
    {
        $fileSystem = $this->StartBuildFileSystem()
            ->addFileWithCallable(
                'namespace-protector-config-full-options.json',
                'json',
                'json',
                function ($directoryReal, $pathFile) {
                    return $this->helperEditChangesFlag($directoryReal, $pathFile, false);
                }
            )
            ->addDirectory('stat_path_in_config')
            ->addDirectory('composer_json_path')
            ->buildFileSystem();

        $fileConfigJson = $fileSystem->url() . '/json/namespace-protector-config-full-options.json';

        $configMaker = new ConfigMaker();
        $configLoaded = $configMaker->createFromFile(new FileSystemPath($fileConfigJson));

        $this->assertEquals(false, $configLoaded->enabledCache());
    }

    private function helperEditChangesFlag($directoryReal, $pathFile, bool $flag)
    {
        $data = \file_get_contents(__DIR__ . '/../../Stub/' . $directoryReal . '/' . $pathFile);
        $data = \json_decode($data, true);
        $data['cache'] = $flag;

        return \json_encode($data);
        return $data;
    }

    /** @test */
    public function it_load_default_config(): void
    {
        $configMaker = new ConfigMaker();
        $configLoaded = $configMaker->createFromFile(
            new FileSystemPath(
                __DIR__
                                . '/..'
                                . '/..'
                                . '/../src/Config/template-config-json'
            )
        );
    }
}
