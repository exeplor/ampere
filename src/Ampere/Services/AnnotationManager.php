<?php

namespace Ampere\Services;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use zpt\anno\Annotations;

/**
 * Class AnnotationManager
 * @package Ampere\Services
 */
class AnnotationManager
{

    /**
     * @param string $path
     * @param string $namespace
     * @return array
     */
    public function getClassAnnotations(string $path, string $namespace): array
    {
        $annotations = [];

        $classes = $this->getClasses($path, $namespace);
        foreach($classes as $class) {
            $classReflector = new \ReflectionClass($class);
            $classAnnotations = $this->getAnnotations($classReflector);

            $annotation = [
                'class' => $class,
                'annotations' => $classAnnotations,
                'methods' => []
            ];

            $methods = $classReflector->getMethods(\ReflectionMethod::IS_PUBLIC);
            $methods = array_filter($methods, function($item) use ($class){
                return $item->class === $class;
            });

            foreach($methods as $method) {
                $methodCommentString = [];

                $comment = $method->getDocComment();
                preg_match_all('/\*\s(?<comment>[a-z\s]+)/si', $comment, $matches);

                foreach($matches['comment'] as $comment) {
                    $comment = trim($comment);
                    if (strlen($comment) > 0) {
                        $methodCommentString[] = $comment;
                    }
                }

                $annotation['methods'][$method->name] = $this->getAnnotations($method);
                $annotation['methods'][$method->name]['__comment'] = implode($methodCommentString);
            }

            $annotations[] = $annotation;
        }

        return $annotations;
    }


    /**
     * @param string $path
     * @param string $namespace
     * @return array
     */
    private function getClasses(string $path, string $namespace): array
    {
        $classes = [];

        /**
         * @var SplFileInfo[] $files
         */
        $files = iterator_to_array(Finder::create()->files()->in($path), false);
        foreach($files as $fileInfo) {
            $class = $namespace;

            if ($fileInfo->getRelativePath()) {
                $class .= '\\' . $fileInfo->getRelativePath();
            }

            $class .= '\\' . $fileInfo->getFilenameWithoutExtension();
            $class = str_replace('/', '\\', $class);

            $classes[] = $class;
        }

        return $classes;
    }

    /**
     * @return array
     */
    private function getAnnotations($reflector): array
    {
        preg_match_all('/@(\w+)(?:\s*(?:\(\s*)?(.*?)(?:\s*\))?)??\s*(?:\n|\*\/)/', $reflector->getDocComment(), $matches);

        $params = [];
        foreach($matches[1] as $id => $key) {
            $value = strlen($matches[2][$id]) === 0 ? 'true' : $matches[2][$id];

            if ($value === 'true') {
                $value = true;
            }

            if ($value === 'false') {
                $value = false;
            }

            $params[$key] = $value;
        }

        return $params;
    }

}