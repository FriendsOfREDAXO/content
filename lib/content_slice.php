<?php

class content_slice
{
    private const VALUE = 'value';
    private const MEDIA = 'media';
    private const MEDIA_LIST = 'medialist';
    private const LINK = 'link';
    private const LINK_LIST = 'linklist';
    /** @var array|string[] */
    private array $data = [];

    public static function factory(): self
    {
        return new self();
    }

    /**
     * @throws rex_exception
     * @return array<string, mixed> data array if not empty
     */
    public function get(): array
    {
        if (0 === count($this->data)) {
            throw new \rex_exception('Data is empty');
        }

        return $this->data;
    }

    /**
     * @throws rex_exception
     */
    public function value(int $id, string $content): self
    {
        $this->checkId($id, 20);
        $this->checkValue(self::VALUE, $id);

        $this->data[$this->getKey(self::VALUE, $id)] = $content;
        return $this;
    }

    /**
     * @throws rex_exception
     * @api
     */
    public function media(int $id, string $media): self
    {
        $this->checkId($id, 10);
        $this->checkValue(self::MEDIA, $id);

        $this->data[$this->getKey(self::MEDIA, $id)] = $media;
        return $this;
    }

    /**
     * @param array<string> $mediaList
     * @throws rex_exception
     * @api
     */
    public function mediaList(int $id, array $mediaList): self
    {
        $this->checkId($id, 10);
        $this->checkValue(self::MEDIA_LIST, $id);

        $this->data[$this->getKey(self::MEDIA_LIST, $id)] = implode(',', $mediaList);
        return $this;
    }

    /**
     * @throws rex_exception
     * @api
     */
    public function link(int $id, int $link): self
    {
        $this->checkId($id, 10);
        $this->checkValue(self::LINK, $id);

        $this->data[$this->getKey(self::LINK, $id)] = $link;
        return $this;
    }

    /**
     * @param array<int> $linkList
     * @throws rex_exception
     * @api
     */
    public function linkList(int $id, array $linkList): self
    {
        $this->checkId($id, 10);
        $this->checkValue(self::LINK_LIST, $id);

        $this->data[$this->getKey(self::LINK_LIST, $id)] = implode(',', $linkList);
        return $this;
    }

    /**
     * @throws rex_exception
     */
    private function checkId(int $id, int $max): void
    {
        if ($id > $max) {
            throw new rex_exception('ID to high...');
        }

        if ($id < 0) {
            throw new rex_exception('ID to low...');
        }
    }

    /**
     * @throws rex_exception
     */
    private function checkValue(string $type, int $id): void
    {
        if ($this->valueExists($type, $id)) {
            throw new rex_exception('Value already exists');
        }
    }

    private function valueExists(string $type, int $id): bool
    {
        return array_key_exists($this->getKey($type, $id), $this->data);
    }

    private function getKey(string $type, int $id): string
    {
        return $type . $id;
    }
}
