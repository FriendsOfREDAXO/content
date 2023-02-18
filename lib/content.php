<?php

class content
{
    /**
     * create a category.
     *
     * @param int|null $status
     * @throws rex_sql_exception|rex_api_exception|rex_exception
     * @return int|null category id on success null on failure
     */
    public static function createCategory(string $name, int|string $categoryId = '', int|string $priority = -1, int|null $status = null): int|null
    {
        $data = [
            'name' => rex_string::sanitizeHtml(trim($name)),
            'catname' => rex_string::sanitizeHtml(trim($name)),
            'catpriority' => $priority,
            'status' => $status,
        ];

        rex_category_service::addCategory($categoryId, $data);

        $sql = rex_sql::factory();
        $sql->setQuery('SELECT id FROM ' . rex::getTable('article') . ' WHERE catname = ? ORDER BY id DESC LIMIT 1', [$name]);

        if (1 === $sql->getRows()) {
            return $sql->getValue('id');
        }

        return null;
    }

    /**
     * create an article.
     *
     * @param int|null $templateId
     * @throws rex_sql_exception|rex_api_exception|rex_exception
     * @return int|null article id on success null on failure
     */
    public static function createArticle(string $name, int $categoryId = 0, int|string $priority = -1, int|null $templateId = null): int|null
    {
        $data = [
            'name' => rex_string::sanitizeHtml(trim($name)),
            'category_id' => $categoryId,
        ];

        if (null === rex_category::get($categoryId) && $categoryId > 0) {
            throw new rex_exception('Category does not exist');
        }

        if (is_int($priority)) {
            $data['priority'] = $priority;
        } elseif ('last' === $priority) {
            $sql = rex_sql::factory();
            $sql->setQuery('SELECT priority FROM ' . rex::getTable('article') . ' WHERE parent_id = ? ORDER BY priority DESC LIMIT 1', [$categoryId]);

            if ($sql->getRows() > 0) {
                $priority = $sql->getValue('priority') + 1;
            } else {
                $priority = 1;
            }

            $data['priority'] = $priority;
        } else {
            $data['priority'] = -1;
        }

        if (null !== $templateId) {
            $data['template_id'] = $templateId;
        } else {
            $data['template_id'] = rex_template::getDefaultId();
        }

        rex_article_service::addArticle($data);

        $sql = rex_sql::factory();
        $sql->setQuery('SELECT id FROM ' . rex::getTable('article') . ' WHERE name = ? ORDER BY id DESC LIMIT 1', [$name]);

        if (1 === $sql->getRows()) {
            return $sql->getValue('id');
        }

        return null;
    }

    /**
     * create a module.
     *
     * @param string|null $key
     * @throws rex_sql_exception
     * @return int the id of the created module
     */
    public static function createModule(string $name, string|null $key = null, string $input = '', string $output = ''): int
    {
        $sql = rex_sql::factory();
        $sql->setTable(rex::getTable('module'));
        $sql->setValue('name', rex_string::sanitizeHtml(trim($name)));
        $sql->setValue('key', $key);
        $sql->setValue('input', rex_string::sanitizeHtml($input));
        $sql->setValue('output', rex_string::sanitizeHtml($output));
        $sql->addGlobalCreateFields();

        $sql->insert();
        $moduleId = (int) $sql->getLastId();
        rex_module_cache::delete($moduleId);

        return $moduleId;
    }

    /**
     * create a template.
     *
     * @param string|null $key
     * @throws rex_sql_exception|rex_exception
     * @return int the id of the created template
     */
    public static function createTemplate(string $name, string|null $key = null, string $content = '', int $active = 1): int
    {
        $attributes = [];
        $attributes['ctype'] = [];
        $attributes['modules'] = [1 => ['all' => '1']];
        $attributes['categories'] = ['all' => '1'];

        if (null !== $key && self::templateKeyExists($key)) {
            throw new rex_exception('Template key already exists');
        }

        $sql = rex_sql::factory();
        $sql->setTable(rex::getTable('template'));
        $sql->setValue('key', $key);
        $sql->setValue('name', rex_string::sanitizeHtml(trim($name)));
        $sql->setValue('active', $active);
        $sql->setValue('content', $content);
        $sql->addGlobalCreateFields();
        $sql->setArrayValue('attributes', $attributes);

        $sql->insert();

        $templateId = (int) $sql->getLastId();
        rex_template_cache::delete($templateId);

        return $templateId;
    }

    /**
     * get the contents from an template by id.
     *
     * @throws rex_sql_exception
     * @return false|string string on success false on failure
     */
    public static function getTemplateContent(int $id): false|string
    {
        $templateSql = rex_sql::factory();
        $templateSql->setQuery('SELECT content FROM ' . rex::getTable('template') . ' WHERE id = ?', [$id]);

        if (0 === $templateSql->getRows()) {
            return false;
        }

        return $templateSql->getValue('content');
    }

    /**
     * update content of an existing template.
     *
     * @throws rex_sql_exception
     */
    public static function setTemplateContent(int $id, string $content): void
    {
        $template = new rex_template($id);
        $templateSql = rex_sql::factory();
        $templateSql->setQuery('SELECT * FROM ' . rex::getTable('template') . ' WHERE id = ?', [$template->getId()]);

        if (1 === $templateSql->getRows()) {
            $sql = rex_sql::factory();
            $sql->setTable(rex::getTable('template'));
            $sql->setWhere(['id' => $template->getId()]);
            $sql->setValue('content', $content);
            $sql->addGlobalUpdateFields();

            try {
                $sql->update();
                rex_template_cache::delete($template->getId());
            } catch (rex_sql_exception $error) {
                if (rex_sql::ERROR_VIOLATE_UNIQUE_KEY === $error->getErrorCode()) {
                    throw new rex_sql_exception(rex_i18n::msg('template_key_exists'));
                }

                throw new rex_sql_exception($error->getMessage());
            }
        }
    }

    /**
     * create a slice.
     *
     * @param array<string, mixed> $data
     * @throws rex_api_exception
     */
    public static function createSlice(int $articleId, int $moduleId, int $clangId, int $ctypeId = 1, array $data = []): void
    {
        rex_content_service::addSlice($articleId, $clangId, $ctypeId, $moduleId, $data);
    }

    /**
     * create a language.
     *
     * @throws rex_sql_exception
     */
    public static function createLanguage(string $code, string $name, int $priority, bool $status = false): false|int
    {
        rex_clang_service::addCLang($code, $name, $priority, $status);

        $sql = rex_sql::factory();
        $sql->setQuery('SELECT id FROM ' . rex::getTable('clang') . ' WHERE `code` = ? LIMIT 1', [$code]);

        if ($sql->getRows() > 0) {
            return $sql->getValue('id');
        }

        return false;
    }

    /**
     * upload media from url.
     *
     * @throws rex_functional_exception
     * @throws rex_socket_exception
     * @return array<string, mixed>|false
     */
    public static function createMediaFromUrl(string $url, string $fileName, int $category = 0): array|false
    {
        if (null === rex_media::get($fileName)) {
            $path = rex_path::media($fileName);
            $media = rex_socket::factoryUrl($url)->doGet();
            $media->writeBodyTo($path);

            return self::createMedia($fileName, $category, $path);
        }

        return false;
    }

    /**
     * create a placeholder image via GD.
     *
     * @throws rex_functional_exception
     * @return array<string, mixed>|false
     */
    public static function createMediaFromGD(string $fileName, int $category = 0, int $width = 500, int $height = 500): array|false
    {
        if (null === rex_media::get($fileName)) {
            $path = rex_path::media($fileName);
            $image = @imagecreate($width, $height);
            imagecolorallocate($image, 255, 0, 255);
            imagejpeg($image, rex_path::media($fileName));
            imagedestroy($image);

            return self::createMedia($fileName, $category, $path);
        }

        return false;
    }

    /**
     * @throws rex_functional_exception
     * @return array<string, mixed>
     */
    private static function createMedia(string $fileName, int $category, string $path): array
    {
        $data = [];
        $data['title'] = '';
        $data['category_id'] = $category;
        $data['file'] = [
            'name' => $fileName,
            'path' => $path,
        ];

        try {
            return rex_media_service::addMedia($data, false);
        } catch (rex_api_exception $e) {
            throw new rex_functional_exception($e->getMessage());
        }
    }

    /**
     * @param string|null $key
     * @throws rex_sql_exception
     */
    private static function templateKeyExists(string|null $key = null): bool
    {
        if (null === $key) {
            return false;
        }

        $templateSql = rex_sql::factory();
        $templateSql->setQuery('SELECT id FROM ' . rex::getTable('template') . ' WHERE `key` = ?', [$key]);

        return 0 !== $templateSql->getRows();
    }
}
