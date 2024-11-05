<?php

namespace App\Dto;

use DateTime;
use Symfony\Component\HttpFoundation\Response;

/**
 * Base class for successful response from API
 */
class ApiResponse
{
    /**
     * @var int
     */
    public int $status;

    /**
     * @var string|null
     */
    public ?string $message;

    /**
     * @var mixed
     */
    public mixed $data;

    /**
     * @var DateTime
     */
    public DateTime $time;

    /**
     * @param string $message
     * @param mixed $data
     * @param int $status
     */
    public function __construct(string $message, mixed $data, int $status = Response::HTTP_OK)
    {
        $this->status = $status;
        $this->message = $message;
        $this->data = $data;
        $this->time = new DateTime();
    }

}