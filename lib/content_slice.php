<?php

class content_slice
{
    /** @var array|string[] */
    private array $data = [];
    private const VALUE = 'value';
    private const MEDIA = 'media';
    private const MEDIA_LIST = 'medialist';
    private const LINK = 'link';
    private const LINK_LIST = 'linklist';

    /**
     * @return content_slice
     */
    public static function factory(): content_slice
    {
        return new self();
    }

    /**
     * @return array<string, mixed> data array if not empty
     * @throws rex_exception
     */
    public function get(): array
    {
        if (sizeof($this->data) === 0) {
            throw new \rex_exception('Data is empty');
        }

        return $this->data;
    }

    /**
     * @param int $id
     * @param string $content
     * @return content_slice
     * @throws rex_exception
     */
    public function value(int $id, string $content): content_slice
    {
        $this->checkId($id, 20);
        $this->checkValue(self::VALUE, $id);

        $this->data[$this->getKey(self::VALUE, $id)] = $content;
        return $this;
    }

    /**
     * @param int $id
     * @param string $media
     * @return content_slice
     * @throws rex_exception
     * @api
     */
    public function media(int $id, string $media): content_slice
    {
        $this->checkId($id, 10);
        $this->checkValue(self::MEDIA, $id);

        $this->data[$this->getKey(self::MEDIA, $id)] = $media;
        return $this;
    }

    /**
     * @param int $id
     * @param array<string> $mediaList
     * @return content_slice
     * @throws rex_exception
     * @api
     */
    public function mediaList(int $id, array $mediaList): content_slice
    {
        $this->checkId($id, 10);
        $this->checkValue(self::MEDIA_LIST, $id);

        $this->data[$this->getKey(self::MEDIA_LIST, $id)] = implode(',', $mediaList);
        return $this;
    }

    /**
     * @param int $id
     * @param int $link
     * @return content_slice
     * @throws rex_exception
     * @api
     */
    public function link(int $id, int $link): content_slice
    {
        $this->checkId($id, 10);
        $this->checkValue(self::LINK, $id);

        $this->data[$this->getKey(self::LINK, $id)] = $link;
        return $this;
    }

    /**
     * @param int $id
     * @param array<int> $linkList
     * @return content_slice
     * @throws rex_exception
     * @api
     */
    public function linkList(int $id, array $linkList): content_slice
    {
        $this->checkId($id, 10);
        $this->checkValue(self::LINK_LIST, $id);

        $this->data[$this->getKey(self::LINK_LIST, $id)] = implode(',', $linkList);
        return $this;
    }

    /**
     * @param int $id
     * @param int $max
     * @return void
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
     * @param string $type
     * @param int $id
     * @return void
     * @throws rex_exception
     */
    private function checkValue(string $type, int $id): void
    {
        if ($this->valueExists($type, $id)) {
            throw new rex_exception('Value already exists');
        }
    }

    /**
     * @param string $type
     * @param int $id
     * @return bool
     */
    private function valueExists(string $type, int $id): bool
    {
        return array_key_exists($this->getKey($type, $id), $this->data);
    }

    /**
     * @param string $type
     * @param int $id
     * @return string
     */
    private function getKey(string $type, int $id): string
    {
        return $type . $id;
    }
}
