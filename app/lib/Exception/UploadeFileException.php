<?php
namespace Projek\Slim\Exception;

class UploadeFileException extends \RuntimeException
{
    public function __construct($code)
    {
        parent::__construct($this->codeToMessage($code), $code);
    }

    private function codeToMessage($code)
    {
        $messages = [
            UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
            UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive',
            UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension',
        ];

        if (isset($messages[$code])) {
            return $messages[$code];
        }

        return 'Unknown upload error';
    }
}
