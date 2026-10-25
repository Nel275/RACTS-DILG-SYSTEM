<?php

namespace App\Services;

use RuntimeException;

/** A stamping failure whose message is safe to show the administrator. */
class StampingException extends RuntimeException
{
}
