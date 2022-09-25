<?php

class rex_content_slice
{
    /** @var array|string[] */
    private array $data = [];

    /**
     */
    public function __construct()
    {
    }

    /**
     * @return rex_content_slice
     */
    public static function factory(): rex_content_slice
    {
        return new self();
    }

    /**
     * @return array
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
     * @return void
     * @throws rex_exception
     */
    public function value(int $id, string $content)
    {
        $this->checkId($id, 20);
    }

    /**
     * @param int $id
     * @param string $media
     * @return void
     * @throws rex_exception
     */
    public function media(int $id, string $media): void
    {
        $this->checkId($id, 10);
    }

    /**
     * @param int $id
     * @param array $media
     * @return void
     * @throws rex_exception
     */
    public function mediaList(int $id, array $media): void
    {
        $this->checkId($id, 10);
    }

    /**
     * @param int $id
     * @param int $link
     * @return void
     * @throws rex_exception
     */
    public function link(int $id, int $link): void
    {
        $this->checkId($id, 10);
    }

    /**
     * @param int $id
     * @param int $linkList
     * @return void
     * @throws rex_exception
     */
    public function linkList(int $id, int $linkList): void
    {
        $this->checkId($id, 10);
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
            throw new rex_exception('Number to high...');
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
    private function getKey(string $type, int $id): string {
        return $type . '_' . $id;
    }
}