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
     * @return void
     * @throws rex_sql_exception|rex_api_exception
     */
    public static function createArticle(string $name, int $categoryId = 0, int|string $priority = -1, int|null $templateId = null): void
    {
        $data = [
            'name' => rex_string::sanitizeHtml(trim($name)),
            'category_id' => $categoryId,
        ];

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

        rex_article_service::addArticle($data);
    }

    /**
     * create a module
     *
     * @param string $name
     * @param string|null $key
     * @param string $input
     * @param string $output
     * @return int
     * @throws rex_sql_exception
     */
    public static function createModule(string $name, string|null $key = null, string $input = '', string $output = ''): int
    {
        $sql = rex_sql::factory();
        $sql->setTable(rex::getTable('module'));
        $sql->setValue('name', rex_string::sanitizeHtml(trim($name)));
        $sql->setValue('key', $key);
        $sql->setValue('input', $input);
        $sql->setValue('output', $output);
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
     * @return int
     * @throws rex_sql_exception
     */
    public static function createTemplate(string $name, string|null $key = null, string $content = '', int $active = 1): int
    {
        $attributes['ctype'] = [];
        $attributes['modules'] = [1 => ['all' => '1']];
        $attributes['categories'] = ['all' => '1'];

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

    public static function createSlice(string $name, int $categoryId = 0, int $priority = -1, int $templateId = null): void
    {
        // TODO: create slice
//        rex_content_service::addSlice();
    }

    public static function createLanguage(string $code, string $name, int $priority, bool $status = false): void
    {
        rex_clang_service::addCLang($code, $name, $priority, $status);
    }


    public static function createMedia(): void
    {
        // TODO: create media from url...
    }
}
