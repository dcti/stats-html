<?php

/**
 * Handles the errors and warnings reported by PHP with exception of:
 * E_ERROR, E_PARSE, E_CORE_*, or E_COMPILE_*.
 *
 * @version 1.0
 */
class ErrorHandler {
    var $error_types = array(
        'WARNING' => 'STATS WARNING',
        'NOTICE' => 'STATS NOTICE',
        'ERROR' => 'STATS ERROR',
        'DEBUG' => 'DEBUG',
    );

    var $verbose = false;
    var $proceed_url = null;
    var $_previous_errors = false;

    /**
     * Construct a new error handler.
     */
    function ErrorHandler($verbose = false, $proceed_url = null) {
        $this->verbose = $verbose;
        $this->proceed_url = $proceed_url;
    }

    /**
     * The error handler callback function.
     *
     * @param errno  the error number.
     * @param errstr  the error message.
     * @param errfile  the file in which the error occured.
     * @param errline  the line number in which the error occured.
     * @param errcontext  the context in which the error occured (array).
     */
    function error_handler($errno, $errstr, $errfile, $errline, $errcontext) {
        # Check if error reporting is turned off.
        if (error_reporting() == 0) {
            return;
        }

        switch ($errno) {
            case E_WARNING:
                $this->inline_error($this->error_types['WARNING'], $errstr, $errfile, $errline, $errcontext);
                break;
            case E_NOTICE:
                $this->inline_error($this->error_types['NOTICE'], $errstr, $errfile, $errline, $errcontext);
                break;
            case E_USER_ERROR:
                $this->halt_error($this->error_types['ERROR'], $errstr, $errfile, $errline, $errcontext);
                break;
            case E_USER_WARNING:
                $this->inline_error($this->error_types['WARNING'], $errstr, $errfile, $errline, $errcontext);
                break;
            case E_USER_NOTICE:
                # Used for debugging.
                $this->inline_error($this->error_types['DEBUG'], $errstr, $errfile, $errline, $errcontext);
                break;
        }

        $this->_previous_errors = true;
    }

    /**
     * Displays an inline error message and continues processing.
     *
     * @param errtype  the error type as string.
     * @param errstr  the error message.
     * @param errfile  the file in which the error occured.
     * @param errline  the line number in which the error occured.
     * @param errcontext  the context in which the error occured (array).
     */
    function inline_error($errtype, $errstr, $errfile, $errline, $errcontext) {
        global $debug;

        echo '<p style="color: red;">' . $errtype . ': ' . nl2br(htmlentities($errstr)) . '</p>';
        if ($this->verbose == true || (isset($debug) && $debug == true)) { # verbose output
            echo '<p style="color: red;">In file <b>' . htmlentities($errfile) . '</b> at line <b>' . $errline . '</b></p>';
        }
    }

    /**
     * Displays an error and halts processing.
     *
     * @param errtype  the error type as string.
     * @param errstr  the error message.
     * @param errfile  the file in which the error occured.
     * @param errline  the line number in which the error occured.
     * @param errcontext  the context in which the error occured (array).
     */
    function halt_error($errtype, $errstr, $errfile, $errline, $errcontext) {
        global $debug;

        $output_buffer = ob_get_contents();
        if (ob_get_length()) {
            ob_end_clean();
        }

        ?>
        <br>
        <center>
            <table border="0" cellspacing="1" cellpadding="0">
                <tr><td><?php echo $errtype; ?></td></tr>
                <tr><td><p style="color: red;"><?php echo nl2br(htmlentities($errstr)); ?></p></td></tr>
                <tr><td><p class="center">
                <?php
                    if ($this->proceed_url === null) {
                        echo 'There was an error processing your request.';
                    } else {
                        echo '<a href="' . $this->proceed_url . '">Proceed</a>';
                    }
                ?>
                </p></td></tr>
                <?php
                    if ($this->verbose == true || (isset($debug) && $debug == true)) { # verbose output
                        ?>
                        <center>
                            <table border="0" cellspacing="0" cellpadding="0">
                                <tr><td>Full path: <?php echo htmlentities($errfile); ?></td></tr>
                                <tr><td>Line: <?php echo $errline; ?></td></tr>
                                <tr><td><?php $this->print_context($errcontext); ?></td></tr>
                            </table>
                        </center>
                        <?php
                        echo '<tr><td>' . $this->print_stack_trace(2); '</tr></td>';
                    }
                ?>
            </table>
        </center>
        <?php

        if ($this->_previous_errors) {
            ?>
            <p>Previous non-fatal errors occurred. Page contents follow.</p>
            <div style="border: solid 1px black; padding: 4px;">
                <?php echo $output_buffer; ?>
            </div>
            <?php
        }

        exit();
    }

    /**
     * Prints the context contained in the variable.
     *
     * @param errcontext  an array containing a context, or a normal variable.
     * @access static
     */
    function print_context($errcontext) {
        ?>
        <table>
            <tr><th>Variable</th><th>Value</th><th>Type</th></tr>
            <?php
            # print normal variables
            foreach ($errcontext as $var => $val) {
                if (!is_array($val) && !is_object($val)) {
                    echo '<tr><td>' . $var . '</td><td>' . htmlentities((string)$val) . '</td><td>' . gettype($val) . '</td></tr>';
                }
            }
            # print arrays
            foreach ($errcontext as $var => $val) {
                if (is_array($val) && ($var != 'GLOBALS')) {
                    echo '<tr><td colspan="3" align="left"><br /><b>' . $var . '</b></td></tr>';
                    echo '<tr><td colspan="3">';
                    $this->print_context($val);
                    echo '</td></tr>';
                }
            }
            ?>
        </table>
        <?php
    }

    /**
     * Prints a stack trace to the current point in the code.
     *
     * @param unshift  the amount of frames to unshift from the stack trace.
     * @access static
     */
    function print_stack_trace($unshift = 1) {
        if (!is_int($unshift)) {
            $unshift = 1;
        }

        $stack = debug_backtrace();

        while ($unshift-- > 0) {
            array_shift($stack);
        }

        ?>
        <center>
            <table>
                <tr><th>Filename</th><th>Line</th><th>Function</th><th>Args</th></tr>
                <?php
                foreach ($stack as $frame) {
                    echo '<tr>';
                    echo '<td>' . htmlentities(isset($frame['file']) ? $frame['file'] : '<php core>') . '</td><td>' . (isset($frame['line']) ? $frame['line'] : '') . '</td><td>' . $frame['function'] . '</td>';
                    $args = array();
                    if (isset($frame['args'])) {
                        foreach($frame['args'] as $value) {
                            $args[] = $this->build_parameter_string($value);
                        }
                    }
                    echo '<td>( ' . htmlentities(implode($args, ', ')) . ' )</td></tr>';
                }
                ?>
            </table>
        </center>
        <?php
    }

    /**
     * Build a parameter list recursively.
     *
     * @param param  any type of variable.
     * @return the parameter list as a string.
     * @access static
     */
    function build_parameter_string($param) {
        if (is_array($param)) {
            $results = array();
            foreach ($param as $key => $value) {
                $results[] = '[' . $this->build_parameter_string($key) . '] => ' . $this->build_parameter_string($value);
            }
            return '{ ' . implode($results, ', ') . ' }';
        } else if (is_bool($param)) {
            if ($param) {
                return 'true';
            } else {
                return 'false';
            }
        } else if (is_float($param) || is_int($param)) {
            return $param;
        } else if (is_null($param)) {
            return 'null';
        } else if (is_object($param)) {
            $results = array();
            $class_name = get_class($param);
            $inst_vars = get_object_vars($param);
            foreach ($inst_vars as $name => $value) {
                $results[] = '[' . $name . '] => ' . $this->build_parameter_string($value);
            }
            return 'Object <' . $class_name . '> ( ' . implode($results, ', ') . ' )';
        } else if (is_string($param)) {
            return "'" . $param . "'";
        }
    }

    /**
     * Returns whether an error or warning has already occured.
     *
     * @return true if an error or warning has occured, false otherwise.
     */
    function error_handled() {
        return $this->_previous_errors;
    }
}

# construct and register the error handler.
# (PHP4 requires the =& operator, but PHP5 uses just = operator.)
$g_error_handler = new ErrorHandler();
set_error_handler(array($g_error_handler, 'error_handler'));

?>
