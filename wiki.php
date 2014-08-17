<?php
class Wiki
{
    /**
     * List of files to generate
     *
     * @var array
     */
    private $files = array();

    /**
     * List of classes to index
     *
     * @var ReflectionClass[]
     */
    private $classes = null;

    /**
     * Current instance
     *
     * @var Wiki
     */
    private static $instance = null;

    /**
     * Returns the current instance of the Wiki generator, or instantiates a new one
     *
     * @return Wiki
     */
    private static function getInstance()
    {
        // if no instance exists yet, instantiate one
        if (self::$instance === null) {
            self::$instance = new Wiki();
        }

        // return the current instance
        return self::$instance;
    }

    /**
     * Retrieves the list of classes to index
     *
     * @return ReflectionClass[]
     */
    private static function getClasses()
    {
        // get the current instance
        $self = self::getInstance();

        // if we don't know the classes yet, determine them
        if ($self->classes === null) {
            $self->classes = array();

            // open the path to the classes
            $path = realpath(dirname(__FILE__) . '/Capirussa/Pathe');
            $dir  = opendir($path);

            // loop through all items in this folder
            while ($file = readdir($dir)) {
                // get the full file path
                $filePath = $path . '/' . $file;

                // check whether this is a readable PHP file
                if (!is_dir($filePath) && is_readable($filePath) && strtolower(substr($filePath, -4)) == '.php') {
                    // include the file
                    /** @noinspection PhpIncludeInspection (too complex for PhpStorm to understand) */
                    require_once($filePath);

                    // store a reflection class for this file
                    $className                 = substr($file, 0, -4);
                    $self->classes[$className] = new ReflectionClass('\\Capirussa\\Pathe\\' . $className);
                }
            }
            closedir($dir);

            // sort the classes by their name, just in case
            ksort($self->classes);
        }

        // return the class list
        return $self->classes;
    }

    /**
     * Returns the comment string without all the DocBlock mess
     *
     * @param string $docComment
     * @return string
     */
    private static function getDocComment($docComment)
    {
        $retValue = array();

        $lines = explode("\n", $docComment);
        foreach ($lines as $line) {
            $line = trim($line);

            // if this line opens or closes the DocBlock or is a blank comment line, skip it
            if ($line == '/**' || $line == '*' || $line == '*/') {
                continue;
            }

            // if this line begins the @param/@return conclusion, stop
            if (preg_match('/^\*\s?@/', $line)) {
                break;
            }

            $retValue[] = trim(substr($line, 1));
        }

        if (count($retValue) > 0) {
            // check whether this comment contains a code example or bullet list
            $isSpecial = false;
            foreach ($retValue as $line) {
                if (substr($line, 0, 2) == '* ' || preg_match('/<code>/', $line)) {
                    $isSpecial = true;
                    break;
                }
            }

            if (!$isSpecial) {
                $retValue = implode(' ', $retValue);
            } else {
                $newRetValue = '';

                $inCode = false;
                $inList = false;

                foreach ($retValue as $line) {
                    if ($line == '<code>') {
                        if ($inList) {
                            $inList = false;
                        }

                        $newRetValue = trim($newRetValue) . "\n\n```php\n";
                        $inCode = true;
                    } elseif ($line == '</code>') {
                        $newRetValue .= "```\n\n";
                        $inCode = false;
                    } elseif ($inCode) {
                        $newRetValue .= $line . "\n";
                    } elseif (!$inList && substr($line, 0, 2) == '* ') {
                        $newRetValue = trim($newRetValue) . "\n\n" . $line . "\n";
                        $inList = true;
                    } elseif ($inList && substr($line, 0, 2) == '* ') {
                        $newRetValue .= $line . "\n";
                    } elseif ($inList && substr($line, 0, 2) != '* ') {
                        $newRetValue = trim($newRetValue) . "\n\n" . $line . ' ';
                        $inList = false;
                    } else {
                        $newRetValue .= $line . ' ';
                    }
                }

                $retValue = trim($newRetValue);
            }
        } else {
            $retValue = '';
        }

        return $retValue;
    }

    /**
     * Gets the comment describing a constant
     *
     * @param ReflectionClass $reflectionClass
     * @param string          $constantName
     * @return string
     */
    private static function getConstantComment($reflectionClass, $constantName)
    {
        $retValue = '';

        // since there is no such thing as a ReflectionConstant, we can't use reflection to determine the DocBlock for
        // a constant. Go go happy parse time!
        $tokens = token_get_all(file_get_contents($reflectionClass->getFileName()));

        $constantIndex = null;
        $docBlockIndex = null;

        // try to find the definition of this constant
        foreach ($tokens as $index => $tokenDetails) {
            if ($tokens[$index - 2][0] == T_CONST && $tokenDetails[1] == $constantName) {
                $constantIndex = $index;
                break;
            }
        }

        // if we found the constant, loop back from there and find the nearest DocBlock comment
        if ($constantIndex !== null) {
            for ($index = $constantIndex; $index >= 0; $index--) {
                if ($tokens[$index][0] == T_DOC_COMMENT || $tokens[$index][0] == T_COMMENT) {
                    $docBlockIndex = $index;
                    break;
                }
            }
        }

        // if we found a comment, check whether it is a DocBlock comment (it may not be)
        if ($docBlockIndex !== null) {
            $comment = $tokens[$docBlockIndex][1];

            if (strpos($comment, '/**') > -1) {
                $retValue = self::getDocComment($comment);
            }
        }

        return $retValue;
    }

    /**
     * Keeps track of which files have been generated
     *
     * @param string $fileName
     * @param string $fileContent
     * @return void
     */
    private static function setFile($fileName, $fileContent)
    {
        $self = self::getInstance();

        $self->files[$fileName] = $fileContent;
    }

    /**
     * Builds the contents for the Home.md file
     *
     * @return void
     */
    public static function writeHome()
    {
        $output = 'Welcome to the pathe-php wiki!' . "\n";
        $output .= "\n";
        $output .= 'You can use this library to communicate with [Mijn Pathé](https://onlinetickets2.pathe.nl/ticketweb.php?sign=30&UserCenterID=1). Everything you want to do is done via the Client object, so for a full list of the possibilities, see the documentation for that class.' . "\n";
        $output .= "\n";
        $output .= 'The library consists of the following objects:' . "\n";

        // loop through all classes to index
        $idx = 0;
        foreach (self::getClasses() as $className => $reflectionClass) {
            $idx++;

            $output .= sprintf(
                '* [%1$s](https://github.com/rickdenhaan/pathe-php/wiki/The-%1$s-object)%2$s%3$s',
                $className,
                $idx < count(self::getClasses()) ? ';' : '.',
                $idx < count(self::getClasses()) ? "\n" : ''
            );
        }

        self::setFile('Home.md', $output);
    }

    /**
     * Builds the contents for all classes
     *
     * @return void
     */
    public static function writeClasses()
    {
        // loop through all classes
        foreach (self::getClasses() as $className => $reflectionClass) {
            $fileName = sprintf(
                'The-%1$s-object.md',
                $className
            );

            $output = self::getDocComment($reflectionClass->getDocComment()) . "\n";
            $output .= "\n";

            // get all constants, properties and methods this class has
            $constants  = $reflectionClass->getConstants();
            $properties = $reflectionClass->getProperties();
            $methods    = $reflectionClass->getMethods();

            // build the document TOC
            if (count($constants) > 0) {
                $output .= '* [Constants](#constants)' . "\n";
            }

            if (count($properties) > 0) {
                $output .= '* [Properties](#properties)' . "\n";
            }

            if (count($methods) > 0) {
                $output .= '* [Methods](#methods)' . "\n";
            }

            // if we have any constants, build that section
            if (count($constants) > 0) {
                $output .= "\n";
                $output .= '# Constants' . "\n";

                ksort($constants);

                // add the section TOC
                foreach ($constants as $constantName => $constantValue) {
                    $output .= sprintf(
                            '* [`%1$s`](#%2$s)',
                            $constantName,
                            strtolower($constantName)
                        ) . "\n";
                }

                // add the constant details
                foreach ($constants as $constantName => $constantValue) {
                    $output .= "\n";

                    $output .= sprintf(
                            '## `%1$s`',
                            $constantName
                        ) . "\n";

                    $output .= self::getConstantComment($reflectionClass, $constantName) . "\n";
                }
            }

            // if we have any properties, build that section
            if (count($properties) > 0) {
                $output .= "\n";
                $output .= '# Properties' . "\n";

                usort(
                    $properties, function ($a, $b) {
                        /* @type $a ReflectionProperty */
                        /* @type $b ReflectionProperty */
                        return strcmp($a->getName(), $b->getName());
                    }
                );

                // add the section TOC
                foreach ($properties as $reflectionProperty) {
                    $output .= sprintf(
                            '* %1$s%2$s%3$s%4$s[$%5$s](#%6$s)',
                            $reflectionProperty->isPrivate() ? '`private` ' : '',
                            $reflectionProperty->isProtected() ? '`protected` ' : '',
                            $reflectionProperty->isPublic() ? '`public` ' : '',
                            $reflectionProperty->isStatic() ? '`static` ' : '',
                            $reflectionProperty->getName(),
                            strtolower($reflectionProperty->getName())
                        ) . "\n";
                }

                // add the property details
                foreach ($properties as $reflectionProperty) {
                    $output .= "\n";

                    $output .= sprintf(
                            '## $%1$s',
                            $reflectionProperty->getName()
                        ) . "\n";

                    $output .= trim(
                            sprintf(
                                '%1$s%2$s%3$s%4$s',
                                $reflectionProperty->isPrivate() ? '`private` ' : '',
                                $reflectionProperty->isProtected() ? '`protected` ' : '',
                                $reflectionProperty->isPublic() ? '`public` ' : '',
                                $reflectionProperty->isStatic() ? '`static` ' : ''
                            )
                        ) . "\n\n";

                    $output .= self::getDocComment($reflectionProperty->getDocComment()) . "\n";
                }
            }

            // if we have any methods, build that section
            if (count($methods) > 0) {
                $output .= "\n";
                $output .= '# Methods' . "\n";

                usort(
                    $methods, function ($a, $b) {
                        /* @type $a ReflectionMethod */
                        /* @type $b ReflectionMethod */
                        return strcmp($a->getName(), $b->getName());
                    }
                );

                // add the section TOC
                foreach ($methods as $reflectionMethod) {
                    $output .= sprintf(
                            '* %1$s%2$s%3$s%4$s[%5$s()](#%6$s)',
                            $reflectionMethod->isPrivate() ? '`private` ' : '',
                            $reflectionMethod->isProtected() ? '`protected` ' : '',
                            $reflectionMethod->isPublic() ? '`public` ' : '',
                            $reflectionMethod->isStatic() ? '`static` ' : '',
                            $reflectionMethod->getName(),
                            strtolower($reflectionMethod->getName())
                        ) . "\n";
                }

                // add the method details
                foreach ($methods as $reflectionMethod) {
                    $output .= "\n";

                    $output .= sprintf(
                            '## %1$s()',
                            $reflectionMethod->getName()
                        ) . "\n";

                    $output .= trim(
                            sprintf(
                                '%1$s%2$s%3$s%4$s',
                                $reflectionMethod->isPrivate() ? '`private` ' : '',
                                $reflectionMethod->isProtected() ? '`protected` ' : '',
                                $reflectionMethod->isPublic() ? '`public` ' : '',
                                $reflectionMethod->isStatic() ? '`static` ' : ''
                            )
                        ) . "\n\n";

                    $output .= self::getDocComment($reflectionMethod->getDocComment()) . "\n";
                }
            }

            self::setFile($fileName, $output);
        }
    }

    /**
     * Outputs the generated files to the given path, or to stdout if no path is given
     *
     * @param string $path (Optional) Defaults to null
     */
    public static function output($path = null)
    {
        $self = self::getInstance();

        foreach ($self->files as $fileName => $fileContent) {
            if ($path === null) {
                echo $fileName . "\n";
                echo str_repeat('-', strlen($fileName)) . "\n\n";
                echo $fileContent . "\n\n\n";
            } else {
                $filePath = realpath($path) . '/' . $fileName;
                echo 'Writing "' . $filePath . '"...' . "\n";

                file_put_contents($filePath, $fileContent);
            }
        }
    }
}

Wiki::writeHome();
Wiki::writeClasses();

$path = null;
if ($argc > 1) {
    $path = $argv[1];

    if (substr($path, 0, 1) !== '/') {
        $path = dirname(__FILE__) . '/' . $path;
    }
}

Wiki::output($path);