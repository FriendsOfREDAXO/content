# Helfer-Addon um Inhalte für REDAXO 5 zu erstellen

Über das Addon __content__ lassen sich Inhalte für eine REDAXO-Instanz einfach und schnell programmatisch erstellen. 

## Methoden

### Article

```php
/**
 * Erstellen eines Artikel
 * return int(Article ID)|null 
 * @param string $name
 * @param int $categoryId (optional)
 * @param int|string $priority (optional)
 * @param int|null $templateId (optional)
 */
content::createArticle('Article Name', int $categoryId = 0, int|string $priority = -1, int|null $templateId = null);
```

### Category

```php
/**
 * Erstellen einer Kategorie
 * return int(Category ID)|null 
 * @param string $name
 * @param int|string $categoryId (optional)
 * @param int|string $priority (optional)
 * @param int|null $status (optional)
 */
content::createCategory('Category Name', int|string $categoryId = '', int|string $priority = -1, int|null $status = null);
```

### Module

```php
/**
 * Erstellen eines Modules
 * return int(Module ID)
 * @param string $name
 * @param string|null $key (optional)
 * @param string $input (optional)
 * @param string $output (optional)
 */
content::createModule('Module Name', string|null $key = null, string $input = '', string $output = '');
```

### Template

```php
/**
 * Erstellen eines Templates
 * return int(Template ID)
 * @param string $name
 * @param string|null $key (optional)
 * @param string $content (optional)
 * @param int $active (optional)
 */
content::createTemplate('Template Name', string|null $key = null, string $content = '', int $active = 1);

/**
 * Inhalte eines Templates holen
 * return false|string
 * @param int $id
 */
content::getTemplateContent($templateID);

/**
 * Inhalte eines Templates setzen
 * @param int $id
 * @param string $content
 */
content::setTemplateContent(1, '<div>Template Inhalt</div');
```

### Media

```php
/**
 * Erstellen eines Bildes für den Medienpool über die GD Library oder von einer URL
 * return array
 */

/**
 * @param string $fileName
 * @param int $category (optional)
 * @param int $width (optional)
 * @param int $height (optional)
*/
content::createMediaFromGD('filename.jpg', int $category = 0, int $width = 500, int $height = 500);

/**
 * @param string $url
 * @param string $fileName
 * @param int $category (optional)
*/
content::createMediaFromUrl('https://url-to-file.de/file.jpg', 'filename.jpg', int $category = 0);
```

### Language

```php
/**
 * Erstellen einer neuen Sprache
 * return false|int
 * @param string $code
 * @param string $name
 * @param int $priority
 * @param bool $status (optional)
 */
content::createLanguage('language_code', 'Language Name', 1, bool $status = false);
```

### Module input/output

```php
$module = content_module::factory();
$module->value(1);
$module->value(2, 'textarea');
$module->link(1);
$module->linkList(1);
$module->media(1);
$module->mediaList(1);

$moduleInput = $module->getInput();
$moduleOutput = $module->getOutput();
```

#### $moduleInput

```html
<div class="form-group">
    <input type="text" class="form-control" name="REX_INPUT_VALUE[1]" value="REX_VALUE[1]" />
</div>
<div class="form-group">
    <textarea class="form-control" name="REX_INPUT_VALUE[2]">REX_VALUE[2]</textarea>
</div>
<div class="form-group">
    REX_LINK[id=1 widget=1]
</div>
<div class="form-group">
    REX_LINKLIST[id=1 widget=1]
</div>
<div class="form-group">
    REX_MEDIA[id=1 widget=1]
</div>
<div class="form-group">
    REX_MEDIALIST[id=1 widget=1]
</div>
```

#### $moduleOutput

```html
<div>REX_VALUE[1]</div>
<div>REX_VALUE[id=2 output="html"]</div>
<div><a href="REX_LINK[id=1 output=url]">Article ID: REX_LINK[id=1]</a></div>
<?php foreach (explode(",", REX_LINKLIST[id=1]) as $articleId): ?>
    <div><a href="<?=rex_getUrl($articleId);?>">Article ID: <?=$articleId;?></a></div>
<?php endforeach;?>
<img src="/media/REX_MEDIA[id=1]" />
<?php foreach (explode(",", REX_MEDIALIST[id=1]) as $image): ?>
    <div><img src ="/media/<?=$image;?>"/></div>
<?php endforeach;?>
```

### Slice content

```php
$slice = content_slice::factory();
$slice->value(1, 'Lorem Ipsum');
$slice->value(2, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.');
$slice->link(1, 5);
$slice->linkList(1, [2,3,4,5]);
$slice->media(1, 'for.png');
$slice->mediaList(1, ['for.png', 'for_1.png', 'for_2.png']);
$sliceContent = $slice->get();

//content::createSlice($articleId, $moduleId, $clangId, $ctypeId, $sliceContent);
```

#### $sliceContent

```php
array(6) {
  ["value1"]=>
  string(11) "Lorem Ipsum"
  ["value2"]=>
  string(231) "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat."
  ["link1"]=>
  int(5)
  ["linklist1"]=>
  string(7) "2,3,4,5"
  ["media1"]=>
  string(7) "for.png"
  ["medialist1"]=>
  string(27) "for.png,for_1.png,for_2.png"
}
```
