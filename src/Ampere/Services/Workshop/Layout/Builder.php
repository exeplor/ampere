<?php

namespace Ampere\Services\Workshop\Layout;

use Ampere\Services\Workshop\Component;
use Ampere\Services\Workshop\Form\Form;
use Ampere\Services\Workshop\Page\Assets;
use Illuminate\Http\Request;

/**
 * Class Builder
 * @package Ampere\Services\Workshop
 */
class Builder
{
    /**
     * @var string
     */
    private $name = 'app';

    /**
     * @var Request
     */
    private $request;

    /**
     * @var string|null
     */
    private $title = null;

    /**
     * @var string|null
     */
    private $content = null;

    /**
     * @var array
     */
    private $styleAssets = [];

    /**
     * @var array
     */
    private $scriptAssets = [];

    /**
     * @param string $name
     * @return Layout
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param Request $request
     * @return Layout
     */
    public function setRequest(Request $request): self
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @param string $title
     * @return Layout
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @param string $content
     * @return Layout
     */
    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @param string $path
     * @return Layout
     */
    public function addCss(string $path): self
    {
        $this->styleAssets[] = $path;
        return $this;
    }

    /**
     * @param string $path
     * @return Layout
     */
    public function addJs(string $path): self
    {
        $this->scriptAssets[] = $path;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return array
     */
    public function getCssAssets(): array
    {
        return $this->styleAssets;
    }

    /**
     * @return array
     */
    public function getJsAssets(): array
    {
        return $this->scriptAssets;
    }

    /**
     * @return Component
     */
    public function makeComponent(): Component
    {
        return new Component($this);
    }

    /**
     * @return Form
     */
    public function makeForm(): Form
    {
        return new Form($this);
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @return Assets
     */
    public function makeAssetManager(): Assets
    {
        return new Assets($this);
    }
}