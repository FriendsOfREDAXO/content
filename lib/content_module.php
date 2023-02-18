<?php

class content_module
{
    private const VALUE = 'value';
    private const MEDIA = 'media';
    private const MEDIA_LIST = 'medialist';
    private const LINK = 'link';
    private const LINK_LIST = 'linklist';
    /** @var array|string[] */
    private array $inputData = [];
    /** @var array|string[] */
    private array $outputData = [];

    public static function factory(): self
    {
        return new self();
    }

    /**
     * @throws rex_exception
     * @return string input as string if not empty
     */
    public function getInput(): string
    {
        if (0 === count($this->inputData)) {
            throw new \rex_exception('Input is empty');
        }

        return implode("\n", $this->inputData);
    }

    /**
     * @throws rex_exception
     * @return string output as string if not empty
     */
    public function getOutput(): string
    {
        if (0 === count($this->outputData)) {
            throw new \rex_exception('Output is empty');
        }

        return implode("\n", $this->outputData);
    }

    /**
     * @throws rex_exception
     */
    public function value(int $id, string $type = 'text'): self
    {
        $this->checkId($id, 20);
        $this->checkValue(self::VALUE, $id);

        $output = '<div>REX_VALUE[' . $id . ']</div>';
        $input = '<input type="text" class="form-control" name="REX_INPUT_VALUE[' . $id . ']" value="REX_VALUE[' . $id . ']" />';

        if ('textarea' === $type) {
            $input = '<textarea class="form-control" name="REX_INPUT_VALUE[' . $id . ']">REX_VALUE[' . $id . ']</textarea>';
            $output = '<div>REX_VALUE[id=' . $id . ' output="html"]</div>';
        }

        $this->inputData[$this->getKey(self::VALUE, $id)] = self::wrapInput($input);
        $this->outputData[$this->getKey(self::VALUE, $id)] = $output;
        return $this;
    }

    /**
     * @throws rex_exception
     * @api
     */
    public function media(int $id): self
    {
        $this->checkId($id, 10);
        $this->checkValue(self::MEDIA, $id);

        $input = 'REX_MEDIA[id=1 widget=1]';
        $output = '<img src="/media/REX_MEDIA[id=' . $id . ']" />';

        $this->inputData[$this->getKey(self::MEDIA, $id)] = self::wrapInput($input);
        $this->outputData[$this->getKey(self::MEDIA, $id)] = $output;
        return $this;
    }

    /**
     * @throws rex_exception
     * @api
     */
    public function mediaList(int $id): self
    {
        $this->checkId($id, 10);
        $this->checkValue(self::MEDIA_LIST, $id);

        $input = 'REX_MEDIALIST[id=' . $id . ' widget=1]';
        $output = '<?php foreach (explode(",", REX_MEDIALIST[id=' . $id . ']) as $image): ?>
                       <div><img src ="/media/<?=$image;?>"/></div>
                   <?php endforeach;?>';

        $this->inputData[$this->getKey(self::MEDIA_LIST, $id)] = self::wrapInput($input);
        $this->outputData[$this->getKey(self::MEDIA_LIST, $id)] = $output;
        return $this;
    }

    /**
     * @throws rex_exception
     * @api
     */
    public function link(int $id): self
    {
        $this->checkId($id, 10);
        $this->checkValue(self::LINK, $id);

        $input = 'REX_LINK[id=' . $id . ' widget=1]';
        $output = '<div><a href="REX_LINK[id=' . $id . ' output=url]">Article ID: REX_LINK[id=' . $id . ']</a></div>';

        $this->inputData[$this->getKey(self::LINK, $id)] = self::wrapInput($input);
        $this->outputData[$this->getKey(self::LINK, $id)] = $output;
        return $this;
    }

    /**
     * @throws rex_exception
     * @api
     */
    public function linkList(int $id): self
    {
        $this->checkId($id, 10);
        $this->checkValue(self::LINK_LIST, $id);

        $input = 'REX_LINKLIST[id=' . $id . ' widget=1]';
        $output = '<?php foreach (explode(",", REX_LINKLIST[id=' . $id . ']) as $articleId): ?>
                       <div><a href="<?=rex_getUrl($articleId);?>">Article ID: <?=$articleId;?></a></div>
                   <?php endforeach;?>';

        $this->inputData[$this->getKey(self::LINK_LIST, $id)] = self::wrapInput($input);
        $this->outputData[$this->getKey(self::LINK_LIST, $id)] = $output;
        return $this;
    }

    /**
     * @return string the wrapped input
     */
    private function wrapInput(string $input): string
    {
        return '<div class="form-group">
                    ' . $input . '
                </div>';
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
        return array_key_exists($this->getKey($type, $id), $this->inputData);
    }

    private function getKey(string $type, int $id): string
    {
        return $type . $id;
    }
}
