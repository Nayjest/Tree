<?php

namespace Nayjest\Tree\Exception;

use RuntimeException;

class ReadonlyNodeModifyException extends RuntimeException implements TreeException
{
}
