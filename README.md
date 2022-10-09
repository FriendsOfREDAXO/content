# Helfer-Addon um Inhalte für REDAXO 5 zu erstellen

Über das Addon __rex_content__ lassen sich Inhalte für eine REDAXO-Instanz einfach und schnell programmatisch erstellen. 

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
rex_content::createArticle('Article Name', int $categoryId = 0, int|string $priority = -1, int|null $templateId = null);
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
rex_content::createCategory('Category Name', int|string $categoryId = '', int|string $priority = -1, int|null $status = null);
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
rex_content::createModule('Module Name', string|null $key = null, string $input = '', string $output = '');
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
rex_content::createTemplate('Template Name', string|null $key = null, string $content = '', int $active = 1);

/**
 * Inhalte eines Templates holen
 * return false|string
 * @param int $id
 */
rex_content::getTemplateContent($templateID);

/**
 * Inhalte eines Templates setzen
 * @param int $id
 * @param string $content
 */
rex_content::setTemplateContent(1, '<div>Template Inhalt</div');
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
rex_content::createMediaFromGD('filename.jpg', int $category = 0, int $width = 500, int $height = 500);

/**
 * @param string $url
 * @param string $fileName
 * @param int $category (optional)
*/
rex_content::createMediaFromUrl('https://url-to-file.de/file.jpg', 'filename.jpg', int $category = 0);
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
rex_content::createLanguage('language_code', 'Language Name', 1, bool $status = false);
```

### Module input/output

```php
$module = rex_content_module::factory();
$module->value(1);
$module->value(2, 'textarea');
$module->link(1);
$module->linkList(1);
$module->media(1);
$module->mediaList(1);

$moduleInput = $module->getInput();
$moduleOutput = $module->getOutput();
```

### Slice content

```php
$slice = rex_content_slice::factory();
$slice->value(1, 'Lorem Ipsum');
$slice->value(2, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.');
$slice->link(1, 5);
$slice->linkList(1, [2,3,4,5]);
$slice->media(1, 'for.png');
$slice->mediaList(1, ['for.png', 'for_1.png', 'for_2.png']);
$sliceContent = $slice->get();

//rex_content::createSlice($articleId, $moduleId, $clangId, $ctypeId, $sliceContent);
```
