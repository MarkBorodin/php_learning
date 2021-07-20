<?php


namespace App\Service;


use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileManagerService implements FileManagerServiceInterface
{
    private $postImageDirectory;

    public function __construct($postImageDirectory)
    {
        $this->postImageDirectory = $postImageDirectory;
    }

    /**
     * @return mixed
     */
    public function getPostImageDirectory()
    {
        return $this->postImageDirectory;
    }


    public function imagePostUpload(UploadedFile $file): string
    {
        // name for file
        $fileName = uniqid().'.'.$file->guessExtension();

        // try to move file to directory
        try
        {
            $file->move($this->getPostImageDirectory(), $fileName);
        }
        catch (FileException $exception)
        {
            return $exception;
        }

        return $fileName;
    }

    public function removePostImage(string $fileName)
    {
        // new file system object
        $fileSystem = new Filesystem();

        // get directory
        $fileImage = $this->getPostImageDirectory().''.$fileName;

        // try to remove file
        try
        {
            $fileSystem->remove($fileImage);
        }
        catch (IOException $exception)
        {
            echo $exception->getMessage();
        }
    }

}