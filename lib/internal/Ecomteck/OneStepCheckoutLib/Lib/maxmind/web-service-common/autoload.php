<?php

/**
 * PSR-4 autoloader implementation for the MaxMind\DB namespace.
 * First we define the 'mmdb_autoload' function, and then we register
 * it with 'spl_autoload_register' so that PHP knows to use it.
 */

/**
 * Automatically include the file that defines <code>class</code>.
 *
 * @param string $class
 *     the name of the class to load
 *
 * @return void
 */
function mm_autoload($class)
{
    /*
    * A project-specific mapping between the namespaces and where
    * they're located. By convention, we include the trailing
    * slashes. The one-element array here simply makes things easy
    * to extend in the future if (for example) the test classes
    * begin to use one another.
    */
    $namespace_map = array('MaxMind\\WebService\\' => __DIR__ . '/src/WebService/');

    foreach ($namespace_map as $prefix => $dir)
    {
        /* First swap out the namespace prefix with a directory... */
        $path = str_replace($prefix, $dir, $class);

        /* replace the namespace separator with a directory separator... */
        $path = str_replace('\\', '/', $path);

        /* and finally, add the PHP file extension to the result. */
        $path = $path . '.php';

        /* $path should now contain the path to a PHP file defining $class */
        @include $path;
    }

    $namespace_map = array('MaxMind\\Exception\\' => __DIR__ . '/src/Exception/');
    foreach ($namespace_map as $prefix => $dir)
    {
        /* First swap out the namespace prefix with a directory... */
        $path = str_replace($prefix, $dir, $class);

        /* replace the namespace separator with a directory separator... */
        $path = str_replace('\\', '/', $path);

        /* and finally, add the PHP file extension to the result. */
        $path = $path . '.php';

        /* $path should now contain the path to a PHP file defining $class */
        @include $path;
    }

}

spl_autoload_register('mm_autoload');