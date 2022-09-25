<?php

beforeEach(function ()
{
//    $uid = uniqid('unittest', false);
//
//    $this->namespace = $uid;
//    $this->key = $uid;
//    $this->value = 'value';
});

test('expect template content to be string', function ()
{
    expect(rex_content::getTemplateContent(1))->toBeString();
});

test('expect template content to be false', function ()
{
    expect(rex_content::getTemplateContent(99))->toBeFalse();
});

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
//    rex_transient::remove($this->namespace, $this->key);
});
