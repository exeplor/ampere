<?php

namespace Ampere\Services;

/**
 * Class StubBuilder
 * @package Ampere\Services
 */
class StubBuilder
{
    /**
     * @var string
     */
    private $stubName;

    /**
     * @var string
     */
    private $targetPath;

    /**
     * @var array
     */
    private $params = [];

    /**
     * @param string $name
     * @return StubBuilder
     */
    public function setStub(string $name): self
    {
        $this->stubName = $name;
        return $this;
    }

    /**
     * @param string $path
     * @return StubBuilder
     */
    public function setTargetPath(string $path): self
    {
        $this->targetPath = $path;
        return $this;
    }

    /**
     * @param array $params
     * @return StubBuilder
     */
    public function setParams(array $params = []): self
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @return bool
     */
    public function isFileExists(): bool
    {
        return file_exists($this->targetPath);
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $templatePath = ampere_path('stubs/' . $this->stubName .  '.stub');
        $templateContent = file_get_contents($templatePath);

        foreach($this->params as $key => $value) {
            $templateContent = str_replace('{' . $key . '}', $value, $templateContent);
        }

        return $templateContent;
    }

    /**
     * @return bool
     */
    public function create(): bool
    {
        $templateContent = $this->render();

        $folder = dirname($this->targetPath);
        @mkdir($folder, 0775, true);

        return file_put_contents($this->targetPath, $templateContent);
    }
}