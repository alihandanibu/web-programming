<?php

/**
 * TEST DOCBLOCK
 */
$ref = new ReflectionClass(stdClass::class);
var_dump($ref->getDocComment());