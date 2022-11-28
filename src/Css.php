<?php

namespace Caasi;

/**
 * Basic structure of a css file
 * @author Isaac Machakata <isaac@caasi.co.zw>
 * @link https://github.com/caasi-co-zw/groomer
 * @version 1.0.0
 */
class Css {
    private $name;
    private $href;
    private $async;
    private $media;
    private $version;

    public function __construct() {
        $this->media = 'all';
        $this->async = true;
    }
    public function setName(string $name) {
        $this->name = $name;
    }
    public function setSource(string $src) {
        $this->href = $src;
    }
    public function setAsynchronous(bool $enable) {
        $this->async = $enable;
    }
    public function setMedia(string $media) {
        $this->media = $media;
    }
    public function setVersion(string $version) {
        $this->version = $version;
    }
    public function getName(): string {
        return $this->name;
    }
    public function getSource(): string {
        return $this->href;
    }
    public function isAsynchronous(): bool {
        return $this->async;
    }
    public function getMedia(): string {
        return $this->media;
    }
    public function getVersion(): string {
        return $this->version;
    }
}
