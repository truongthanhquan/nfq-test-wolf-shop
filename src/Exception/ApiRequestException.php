<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpClient\Exception\TransportException;

class ApiRequestException extends TransportException
{
}
