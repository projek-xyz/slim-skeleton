<?php
namespace Projek\Slim;

use Projek\Slim\Exception\UploadeFileException;
use Psr\Http\Message\UploadedFileInterface;

class Uploader
{
    protected $settings = [
        'directory' => null,
        'extensions' => []
    ];

    /**
     * @param  array $settings
     */
    public function __construct(array $settings = [])
    {
        $this->settings = array_merge($this->settings, $settings);
    }

    /**
     * @param  UploadedFileInterface $file
     */
    public function __invoke(UploadedFileInterface $file)
    {
        if ($file->getError() !== UPLOAD_ERR_OK) {
            throw new UploadeFileException($file->getError());
        }

        if (!in_array($file->getClientMediaType(), $this->settings['extensions'])) {
            throw new \InvalidArgumentException('Filetype not allowed');
        }

        $this->upload($file);
    }

    /**
     * @param  UploadedFileInterface $file
     */
    protected function upload(UploadedFileInterface $file)
    {
        $file->moveTo($this->settings['directory'].'/'.$file->getClientFilename());
    }
}
