<?php

final class Autoload
{
    private static $classMap = [];
    private static $protectedPath;

    public static function load(string $className)
    {
        if (isset(self::$classMap[$className])) {
            return false;
        }
        $classFile = self::getClassFile($className);
        if (file_exists($classFile)) {
            require_once $classFile;
            self::$classMap[$className] = $classFile;
        }
        return false;
    }

    public static function init()
    {
        self::$protectedPath = PUBLIC_PATH . '/../protected';
    }

    /**
     * @param string $className
     * @return string
     */
    public static function getClassFile(string $className): string
    {
        $className = str_replace('\\', '/', $className);
        if (($strpos = strpos($className, 'L/')) === 0) {
            $classFile = L_PATH . '/' . substr($className, $strpos + 2) . '.php';
        } else {
            if ($className[0] !== '\\') {
                $className = '\\' . $className;
            }
            $classFile = self::$protectedPath . $className . '.php';
        }
        return $classFile;
    }
}
