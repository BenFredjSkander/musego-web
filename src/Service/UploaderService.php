<?php

namespace App\Service;

use Cloudinary\Api\Exception\ApiError;
use Cloudinary\Cloudinary;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploaderService
{
    private LoggerInterface $logger;
    private string $cloud_name;
    private string $api_key;
    private string $api_secret;

    /**
     * @param LoggerInterface $logger
     * @param string $cloud_name
     * @param string $api_key
     * @param string $api_secret
     */
    public function __construct(LoggerInterface $logger, string $cloud_name, string $api_key, string $api_secret)
    {
        $this->logger = $logger;
        $this->cloud_name = $cloud_name;
        $this->api_key = $api_key;
        $this->api_secret = $api_secret;
    }


    public function upload(UploadedFile $file): string
    {


        $fileName = $file->getRealPath();
        $cloudinary = new Cloudinary(
            [
                'cloud' => [
                    'cloud_name' => $this->cloud_name,
                    'api_key' => $this->api_key,
                    'api_secret' => $this->api_secret
                ],
                'url' => ['secure' => true],
            ]
        );

        try {
            $imageUploaded = $cloudinary->uploadApi()->upload(
                $fileName);
        } catch (ApiError $e) {
            $this->logger->critical($e->getMessage());
            throw new UploadException();
        }

        return $imageUploaded['secure_url'];
    }
}
