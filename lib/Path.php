<?php

namespace Befew;

class Path {
    private string $path;

    public function __construct(...$path) {
        $this->path = implode(
            DIRECTORY_SEPARATOR,
            array_map(fn ($pathPart) => rtrim($pathPart, DIRECTORY_SEPARATOR), $path)
        );
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

    public function getPathWithTrailingSlash(): string {
        return rtrim($this->getPath(), '\\/') . DIRECTORY_SEPARATOR;
    }

    public function withoutTrailingSlash(): Path {
        return $this->setPath($this->getPathWithoutTrailingSlash());
    }

    public function withTrailingSlash(): Path {
        return $this->setPath($this->getPathWithTrailingSlash());
    }

    public function concat(...$path): Path {
        $this->setPath(
            $this->getPathWithTrailingSlash() . implode(
                DIRECTORY_SEPARATOR,
                array_map(fn ($pathPart) => rtrim($pathPart, DIRECTORY_SEPARATOR), $path)
            )
        );

        return $this;
    }
}