<?php

namespace ashish336b\PhpCBF\abstraction;

use ashish336b\PhpCBF\Request;
use ashish336b\PhpCBF\Response;

interface IDispatch
{
   public function dispatch(Request $request, Response $response);
}
