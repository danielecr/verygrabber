<?php

function throwErrorException($errstr = null,$code = null, $errno = null, $errfile = null, $errline = null) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}
function warning_handler($errno, $errstr, $errfile, $errline, array $errcontext) {
    return false && throwErrorException($errstr, 0, $errno, $errfile, $errline);
    # error_log("AAA"); # will never run after throw
    /* Do execute PHP internal error handler */
    # return false; # will never run after throw
}
  
set_error_handler('warning_handler', E_NOTICE);
