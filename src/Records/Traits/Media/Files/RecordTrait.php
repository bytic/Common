<?php

namespace ByTIC\Common\Records\Traits\Media\Files;

use ByTIC\Common\Records\Media\Files\Model as ModelFile;
use Nip\Filesystem\FileDisk;

/**
 * Class RecordTrait
 * @package ByTIC\Common\Records\Traits\Media\Files
 */
trait RecordTrait
{

    use \ByTIC\Common\Records\Traits\AbstractTrait\RecordTrait;

    /**
     * @var ModelFile[]
     */
    public $files = null;

    /**
     * @param ModelFile $file
     * @return string
     */
    public function getFileURL($file)
    {
        return $this->getUploadURL() . $file->getName();
    }

    /**
     * @param $fileData
     * @return bool|ModelFile
     */
    public function uploadFile($fileData)
    {
        $file = $this->getNewFile();

        if ($file->upload($fileData)) {
            return $file;
        }

        return false;
    }

    /**
     * File factory
     *
     * @param null $type
     *
     * @return ModelFile
     */
    public function getNewFile($type = null)
    {
        $class = $this->getFileModelName($type);
        /** @var ModelFile $file */
        $file = new $class();
        $file->setModel($this);
        $file->setFilesystem($this->getFilesystemDisk());
        return $file;
    }

    /**
     * Get the default files disk instance for current model
     *
     * @return FileDisk
     */
    public function getFilesystemDisk()
    {
        return app('filesystem')->disk($this->getFilesystemDiskName());
    }

    /**
     * @return string
     */
    public function getFilesystemDiskName()
    {
        return 'public';
    }

    /**
     * @param null $type
     * @return string
     */
    public function getFileModelName($type = null)
    {
        $name = $this->isNamespaced() ? $this->getFileModelNamespaced($type) : $this->getFileModelNameDefault($type);

        return $name;
    }

    /**
     * @param null $type
     * @return string
     */
    public function getFileModelNamespaced($type = null)
    {
        $type = $type ? $type : 'Generic';

        return $this->getManager()->getModelNamespace() . 'Files\\' . ucfirst($type);
    }

    /**
     * @param null $type
     * @return string
     */
    public function getFileModelNameDefault($type = null)
    {
        if ($type) {
            return $this->getManager()->getModel() . "_File_" . ucfirst($type);
        }

        return $this->getManager()->getModel() . "_File";
    }

    /**
     * @param $request
     * @return bool
     */
    public function removeFile($request)
    {
        $this->checkFiles();

        if ($this->files[$request['file']]) {
            $this->files[$request['file']]->delete();
        }

        return true;
    }

    /**
     * Check if files have been inited
     *
     * @return ModelFile[]|null
     */
    public function checkFiles()
    {
        if ($this->files === null) {
            $this->findFiles();
        }
        return $this->files;
    }

    /**
     * Find files
     *
     * @return void
     */
    public function findFiles()
    {
        $files = $this->getFilesystemDisk()->listContents($this->getFilesPath());
        foreach ($files as $fileData) {
            $this->addNewFileFromArray($fileData);
        }
    }

    /**
     * @return string
     */
    public function getFilesPath()
    {
        return '/files/' . $this->getManager()->getTable() . '/' . $this->id . '/';
    }

    /**
     * @param $data
     */
    public function addNewFileFromArray($data)
    {
        $file = $this->getNewFile();
        $file->setPath($data['path']);
        $this->appendFile($file);
    }

    /**
     * @param ModelFile $file
     */
    public function appendFile($file)
    {
        $this->files[$file->getName()] = $file;
    }

    /**
     * @return ModelFile[]
     */
    public function getFiles(): array
    {
        $this->checkFiles();
        return $this->files;
    }

    /**
     * @param array $files
     */
    public function setFiles($files = [])
    {
        if ($files) {
            foreach ($files as $name) {
                $file = $this->getNewFile();
                $file->setName($name);

                $this->files[$name] = $file;
            }
        }
    }
}
