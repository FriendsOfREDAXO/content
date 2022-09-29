<?php

class rex_content
{
    /**
     * create an article
     *
     * @param string $name
     * @param int $categoryId
     * @param int|string $priority
     * @param int|null $templateId
     * @return int|null article id on success null on failure
     * @throws rex_sql_exception|rex_api_exception|rex_exception
     */
    public static function createArticle(string $name, int $categoryId = 0, int|string $priority = -1, int|null $templateId = null): int|null
    {
        $data = [
            'name' => rex_string::sanitizeHtml(trim($name)),
            'category_id' => $categoryId,
        ];

        if (rex_category::get($categoryId) === null && $categoryId > 0) {
            throw new rex_exception('Category does not exist');
        }

        if (is_int($priority)) {
            $data['priority'] = $priority;
        }
        elseif ($priority === 'last') {
            $sql = rex_sql::factory();
            $sql->setQuery('SELECT priority FROM ' . rex::getTable('article') . ' WHERE parent_id = ? ORDER BY priority DESC LIMIT 1', [$categoryId]);

            if ($sql->getRows()) {
                $priority = (int)$sql->getValue('priority') + 1;
            }
            else {
                $priority = 1;
            }

            $data['priority'] = $priority;
        }
        else {
            $data['priority'] = -1;
        }

        if ($templateId !== null) {
            $data['template_id'] = $templateId;
        }
        else {
            $data['template_id'] = rex_template::getDefaultId();
        }

        rex_article_service::addArticle($data);

        $sql = rex_sql::factory();
        $sql->setQuery('SELECT id FROM ' . rex::getTable('article') . ' WHERE name = ? ORDER BY id DESC LIMIT 1', [$name]);

        if ($sql->getRows() === 1) {
            return (int)$sql->getValue('id');
        }

        return null;
    }

    /**
     * create a module
     *
     * @param string $name
     * @param string|null $key
     * @param string $input
     * @param string $output
     * @return int the id of the created module
     * @throws rex_sql_exception
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
        $moduleId = (int)$sql->getLastId();
        rex_module_cache::delete($moduleId);

        return $moduleId;
    }

    /**
     * create a template
     *
     * @param string $name
     * @param string|null $key
     * @param string $content
     * @param int $active
     * @return int the id of the created template
     * @throws rex_sql_exception|rex_exception
     */
    public static function createTemplate(string $name, string|null $key = null, string $content = '', int $active = 1): int
    {
        $attributes['ctype'] = [];
        $attributes['modules'] = [1 => ['all' => '1']];
        $attributes['categories'] = ['all' => '1'];

        if ($key !== null && self::templateKeyExists($key)) {
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

        $templateId = (int)$sql->getLastId();
        rex_template_cache::delete($templateId);

        return $templateId;
    }

    /**
     * get the contents from an template by id
     *
     * @param int $id
     * @return false|string string on success false on failure
     * @throws rex_sql_exception
     */
    public static function getTemplateContent(int $id): false|string
    {
        $templateSql = rex_sql::factory();
        $templateSql->setQuery('SELECT content FROM ' . rex::getTable('template') . ' WHERE id = ?', [$id]);

        if ($templateSql->getRows() === 0) {
            return false;
        }

        return $templateSql->getValue('content');
    }

    /**
     * update content of an existing template
     *
     * @param int $id
     * @param string $content
     * @return void
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
            }
            catch (rex_sql_exception $error) {
                if (rex_sql::ERROR_VIOLATE_UNIQUE_KEY === $error->getErrorCode()) {
                    throw new rex_sql_exception(rex_i18n::msg('template_key_exists'));
                }

                throw new rex_sql_exception($error->getMessage());
            }
        }
    }

    /**
     * create a slice
     *
     * @param int $articleId
     * @param int $moduleId
     * @param int $clangId
     * @param int $ctypeId
     * @param array $data
     * @return void
     * @throws rex_api_exception
     */
    public static function createSlice(int $articleId, int $moduleId, int $clangId, int $ctypeId = 1, array $data = []): void
    {
        rex_content_service::addSlice($articleId, $clangId, $ctypeId, $moduleId, $data);
    }

    /**
     * create a language
     *
     * @param string $code
     * @param string $name
     * @param int $priority
     * @param bool $status
     * @return void
     */
    public static function createLanguage(string $code, string $name, int $priority, bool $status = false): void
    {
        rex_clang_service::addCLang($code, $name, $priority, $status);
    }

    /**
     * upload media from url
     *
     * @param string $url
     * @param string $fileName
     * @param int $category
     * @return void
     * @throws rex_functional_exception
     * @throws rex_socket_exception
     * @throws rex_exception
     */
    public static function createMediaFromUrl(string $url, string $fileName, int $category = 0): void
    {
        if (rex_media::get($fileName) === null) {
            $path = rex_path::media($fileName);
            $media = rex_socket::factoryUrl($url)->doGet();
            $media->writeBodyTo($path);

            self::createMedia($fileName, $category, $path);
        }
    }

    /**
     * create a placeholder image via GD
     *
     * @param string $fileName
     * @param int $category
     * @param int $width
     * @param int $height
     * @return void
     * @throws rex_functional_exception
     */
    public static function createMediaFromGD(string $fileName, int $category = 0, int $width = 500, int $height = 500): void
    {
        if (rex_media::get($fileName) === null) {
            $path = rex_path::media($fileName);
            $image = @imagecreate($width, $height);
            imagecolorallocate($image, 255, 0, 255);
            imagejpeg($image, rex_path::media($fileName));
            imagedestroy($image);

            self::createMedia($fileName, $category, $path);
        }
    }

    /**
     * @param string $fileName
     * @param int $category
     * @param string $path
     * @return void
     * @throws rex_functional_exception
     */
    private static function createMedia(string $fileName, int $category, string $path): void
    {
        $data = [];
        $data['title'] = '';
        $data['category_id'] = $category;
        $data['file'] = [
            'name' => $fileName,
            'path' => $path,
        ];

        try {
            rex_media_service::addMedia($data, false);
        }
        catch (rex_api_exception $e) {
            throw new rex_functional_exception($e->getMessage());
        }
    }

    /**
     * @param string|null $key
     * @return bool
     * @throws rex_sql_exception
     */
    private static function templateKeyExists(string|null $key = null): bool
    {
        if ($key === null) {
            return false;
        }

        $templateSql = rex_sql::factory();
        $templateSql->setQuery('SELECT id FROM ' . rex::getTable('template') . ' WHERE `key` = ?', [$key]);

        return $templateSql->getRows() !== 0;
    }
}
