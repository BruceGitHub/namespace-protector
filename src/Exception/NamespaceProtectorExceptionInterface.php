<?php
declare(strict_types=1);

namespace NamespaceProtector\Exception;

interface NamespaceProtectorExceptionInterface
{
    public const MSG_PLAINE_JSON_EXCEPTION = 'Error json operation';
    public const MSG_PLAIN_ERROR_FILE_GET_CONTENT = 'Error while file_get_contents';
    public const MSG_PLAIN_ERROR_PHP_PARSE_JSON_DECODE = 'Error during PhpParse JsonDecoder';
}
