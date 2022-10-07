<?php
$templateKey = uniqid('template_', false);

beforeEach(function ()
{
//    $uid = uniqid('unittest', false);
//
//    $this->namespace = $uid;
//    $this->key = $uid;
//    $this->value = 'value';
});

/**
 * template
 */
test('expect template content to be string', function ()
{
    expect(rex_content::getTemplateContent(1))->toBeString();
});

test('expect template content to be false', function ()
{
    expect(rex_content::getTemplateContent(99))->toBeFalse();
});

test('expect template id', function () use ($templateKey)
{
    expect(rex_content::createTemplate('Template Name', $templateKey))->toBeInt();
});

test('expect template key already exists exception', function () use ($templateKey)
{
    rex_content::createTemplate('Template Name 2', $templateKey);
})->throws(rex_exception::class, 'Template key already exists');

test('expect to set template content', function () use ($templateKey)
{
    $template = rex_template::forKey($templateKey);
    expect(rex_content::setTemplateContent($template->getId(), 'Lorem Ipsum'))
        ->not->toThrow(rex_sql_exception::class);
});

/**
 * article
 */
test('expect category does not exists exception', function ()
{
    rex_content::createArticle('Article Name', 99);
})->throws(rex_exception::class, 'Category does not exist');

test('expect article id', function ()
{
    expect(rex_content::createArticle('Article Name'))->toBeInt();
});

test('expect clang id', function ()
{
    expect(rex_content::createLanguage('xy', 'XY', 1))->toBeInt();
});

test('expect media array from GD', function ()
{
    expect(rex_content::createMediaFromGD('gd_image.jpg'))->toBeArray();
});

test('expect media array from URL', function ()
{
    expect(rex_content::createMediaFromUrl('https://raw.githubusercontent.com/FriendsOfREDAXO/friendsofredaxo.github.io/assets/v2/FOR-avatar-03.png', 'url_image.jpg'))->toBeArray();
});

afterEach(function ()
{
});
