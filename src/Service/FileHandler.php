<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileHandler
{
    public function __construct(
        private readonly string $imagesDirectory,
        private readonly SluggerInterface $slugger,
    ) {
    }

    public function upload(UploadedFile $uploadedFile): string
    {
        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $unicodeString = $this->slugger->slug($originalFilename);
        $fileName = $unicodeString.'-'.uniqid().'.'.$uploadedFile->guessExtension();

        try {
            $uploadedFile->move($this->getImageDirectory(), $fileName);
        } catch (FileException $fileException) {
            throw new UploadException($fileException->getMessage());
        }

        return $fileName;
    }

    public function getImageDirectory(): string
    {
        return $this->imagesDirectory;
    }
}
