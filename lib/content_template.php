<?php

class content_template
{
    public function get(string $title = ''): string
    {
        return '
        <!doctype html>
        <html class="no-js" lang="">
            <head>
              <meta charset="utf-8">
              <title>' . $title . '</title>
              <meta name="description" content="">
              <meta name="viewport" content="width=device-width, initial-scale=1">
            </head>
            <body>
                REX_ARTICLE[]
            </body>
        </html>';
    }
}
