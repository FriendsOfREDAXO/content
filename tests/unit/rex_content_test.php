<?php

$templateKey = uniqid('template_', false);

beforeEach(static function () {
    //    $uid = uniqid('unittest', false);
    //
    //    $this->namespace = $uid;
    //    $this->key = $uid;
    //    $this->value = 'value';
});

/**
 * template.
 */
test('expect template content to be string', static function () {
    expect(content::getTemplateContent(1))->toBeString();
});

test('expect template content to be false', static function () {
    expect(content::getTemplateContent(99))->toBeFalse();
});

test('expect template id', static function () use ($templateKey) {
    expect(content::createTemplate('Template Name', $templateKey))->toBeInt();
});

test('expect template key already exists exception', static function () use ($templateKey) {
    content::createTemplate('Template Name 2', $templateKey);
})->throws(rex_exception::class, 'Template key already exists');

test('expect to set template content', static function () use ($templateKey) {
    $template = rex_template::forKey($templateKey);
    expect(content::setTemplateContent($template->getId(), 'Lorem Ipsum'))
        ->not->toThrow(rex_sql_exception::class);
});

/**
 * article.
 */
test('expect category does not exists exception', static function () {
    content::createArticle('Article Name', 99);
})->throws(rex_exception::class, 'Category does not exist');

test('expect article id', static function () {
    expect(content::createArticle('Article Name'))->toBeInt();
});

/**
 * category.
 */
test('expect category id', static function () {
    expect(content::createCategory('Category Name'))->toBeInt();
});

/**
 * language.
 */
test('expect clang id', static function () {
    expect(content::createLanguage('xy', 'XY', 2))->toBeInt();
});

/**
 * media.
 */
test('expect media array from GD', static function () {
    expect(content::createMediaFromGD('gd_image.jpg'))->toBeArray();
});

test('expect media array from URL', static function () {
    expect(content::createMediaFromUrl('https://raw.githubusercontent.com/FriendsOfREDAXO/friendsofredaxo.github.io/assets/v2/FOR-avatar-03.png', 'url_image.jpg'))->toBeArray();
});

/**
 * content slice.
 */
test('expect array from content slice', static function () {
    $slice = content_slice::factory();
    $slice->value(1, 'Lorem Ipsum');
    expect($slice->get())->toBeArray();
});

test('expect content_slice value already exists exception', static function () {
    $slice = content_slice::factory();
    $slice->value(1, 'Lorem Ipsum');
    $slice->value(1, 'Lorem Ipsum');
})->throws(rex_exception::class, 'Value already exists');

/**
 * content module.
 */
test('expect input string from content module', static function () {
    $module = content_module::factory();
    $module->value(1);
    $module->getInput();
    expect($module->getInput())->toBeString();
});

test('expect output string from content module', static function () {
    $module = content_module::factory();
    $module->value(1);
    $module->getInput();
    expect($module->getOutput())->toBeString();
});

test('expect content_module value already exists exception', static function () {
    $module = content_module::factory();
    $module->value(1);
    $module->value(1);
})->throws(rex_exception::class, 'Value already exists');

afterEach(static function () {
});
