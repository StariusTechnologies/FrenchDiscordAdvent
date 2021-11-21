<?php

namespace Befew;

class Path {
    private string $path;

    public function __construct(string $path) {
        $this->path = $path;
    }

    public function __toString(): string {
        return $this->getPath();
    }

    public function getPath(): string {
        return $this->path;
    }

    public function setPath(string $path): Path {
        $this->path = $path;

        return $this;
    }

    public function getPathWithWebSeparators(): string {
        return str_replace('\\', '/', $this->getPath());
    }

    public function withWebSeparators(): Path {
        return $this->setPath($this->getPathWithWebSeparators());
    }

    public function getPathWithoutTrailingSlash(): string {
        return rtrim($this->getPath(), '\\/');
    }

    public function withoutTrailingSlash(): Path {
        return $this->setPath($this->getPathWithoutTrailingSlash());
    }
}