<?php

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

test('expect template id', function ()
{
    expect(rex_content::createTemplate('Template Name', 'template_key'))->toBeInt();
});

test('expect template key already exists exception', function ()
{
    rex_content::createTemplate('Template Name 2', 'template_key');
})->throws(rex_exception::class, 'Template key already exists');

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

afterEach(function ()
{
});
