<?php
namespace pmill\Doctrine\Rest\Helper;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

class FileHelper
{
    /**
     * @param $folder
     * @param string $pattern
     * @return array
     */
    public function findFilesInFolder($folder, $pattern = '/\.php$/')
    {
        $dir = new RecursiveDirectoryIterator($folder);
        $ite = new RecursiveIteratorIterator($dir);
        $files = new RegexIterator($ite, $pattern, RegexIterator::GET_MATCH);
        return array_keys(iterator_to_array($files));
    }

    /**
     * @param $filename
     * @return array
     */
    public function findClassesInFile($filename)
    {
        if (!file_exists($filename)) {
            return [];
        }

        $source = file_get_contents($filename);

        $classes = array();

        $namespace = 0;
        $tokens = token_get_all($source);
        $count = count($tokens);
        $dlm = false;

        for ($i = 2; $i < $count; $i++) {
            if ((isset($tokens[$i - 2][1])
                && ($tokens[$i - 2][1] == "phpnamespace" || $tokens[$i - 2][1] == "namespace"))
                || ($dlm && $tokens[$i - 1][0] == T_NS_SEPARATOR && $tokens[$i][0] == T_STRING)) {

                if (!$dlm) {
                    $namespace = 0;
                }

                if (isset($tokens[$i][1])) {
                    $namespace = $namespace ? $namespace . "\\" . $tokens[$i][1] : $tokens[$i][1];
                    $dlm = true;
                }
            } elseif ($dlm && ($tokens[$i][0] != T_NS_SEPARATOR) && ($tokens[$i][0] != T_STRING)) {
                $dlm = false;
            }

            if (($tokens[$i - 2][0] == T_CLASS
                || (isset($tokens[$i - 2][1]) && $tokens[$i - 2][1] == "phpclass"))
                && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING) {
                $class_name = $tokens[$i][1];

                if (!isset($classes[$namespace])) {
                    $classes[$namespace] = array();
                }

                $classes[$namespace][] = $class_name;
            }
        }

        return $classes;
    }
}
