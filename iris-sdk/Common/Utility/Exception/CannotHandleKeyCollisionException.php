<?php

namespace Barbondev\IRISSDK\Common\Utility\Exception;

use Guzzle\Common\Exception\GuzzleException;

class CannotHandleKeyCollisionException extends \RuntimeException implements GuzzleException {}