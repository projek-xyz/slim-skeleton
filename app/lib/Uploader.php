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

        if (null === $this->settings['directory']) {
            $this->settings['directory'] = STORAGE_DIR.'uploads';
        }
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

        if ($file->getSize() > sizes_to_bites(ini_get('upload_max_filesize'))) {
            throw new \InvalidArgumentException('Filesize is too big');
        }

        $this->validateDirectory();

        $this->upload($file);
    }

    protected function validateDirectory()
    {
        /** @var \League\Flysystem\Filesystem $fs */
        $fs = app('filesystem');
        $path = str_replace(STORAGE_DIR, '', $this->settings['directory']);

        if (!$fs->has($path)) {
            $fs->createDir($path);
        }
    }

    /**
     * @param  UploadedFileInterface $file
     */
    protected function upload(UploadedFileInterface $file)
    {
        $file->moveTo($this->settings['directory'].'/'.$file->getClientFilename());
    }
}
