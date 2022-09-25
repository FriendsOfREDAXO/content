<?php

class rex_content_slice
{
    /** @var array|string[] */
    private array $data = [];
    private const VALUE = 'value';
    private const MEDIA = 'media';
    private const MEDIA_LIST = 'medialist';
    private const LINK = 'link';
    private const LINK_LIST = 'linklist';

    /**
     * @return rex_content_slice
     */
    public static function factory(): rex_content_slice
    {
        return new self();
    }

    /**
     * @return array data array if not empty
     * @throws rex_exception
     */
    public function get(): array
    {
        if (empty($this->data)) {
            throw new \rex_exception('Data is empty');
        }

        return $this->data;
    }

    /**
     * @param int $id
     * @param string $content
     * @return rex_content_slice
     * @throws rex_exception
     */
    public function value(int $id, string $content): rex_content_slice
    {
        $this->checkId($id, 20);
        $this->checkValue(self::VALUE, $id);

        $this->data[$this->getKey(self::VALUE, $id)] = $content;
        return $this;
    }

    /**
     * @param int $id
     * @param string $media
     * @return rex_content_slice
     * @throws rex_exception
     */
    public function media(int $id, string $media): rex_content_slice
    {
        $this->checkId($id, 10);
        $this->checkValue(self::MEDIA, $id);

        $this->data[$this->getKey(self::MEDIA, $id)] = $media;
        return $this;
    }

    /**
     * @param int $id
     * @param array $mediaList
     * @return rex_content_slice
     * @throws rex_exception
     */
    public function mediaList(int $id, array $mediaList): rex_content_slice
    {
        $this->checkId($id, 10);
        $this->checkValue(self::MEDIA_LIST, $id);

        $this->data[$this->getKey(self::MEDIA_LIST, $id)] = implode(',', $mediaList);
        return $this;
    }

    /**
     * @param int $id
     * @param int $link
     * @return rex_content_slice
     * @throws rex_exception
     */
    public function link(int $id, int $link): rex_content_slice
    {
        $this->checkId($id, 10);
        $this->checkValue(self::LINK, $id);

        $this->data[$this->getKey(self::LINK, $id)] = $link;
        return $this;
    }

    /**
     * @param int $id
     * @param array $linkList
     * @return rex_content_slice
     * @throws rex_exception
     */
    public function linkList(int $id, array $linkList): rex_content_slice
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
