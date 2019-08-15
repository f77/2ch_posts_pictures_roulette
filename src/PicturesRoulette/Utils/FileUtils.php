<?php

namespace PicturesRoulette\Utils;

trait FileUtils
{
    /**
     * @param string $_dir
     *
     * @return array
     */
    public function getDirectoryFilesRecursive(string $_dir): array
    {
        $result = [];
        $iter = new \RecursiveIteratorIterator (
            new \RecursiveDirectoryIterator ($_dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST,
            \RecursiveIteratorIterator::CATCH_GET_CHILD // Ignore "Permission denied"
        );

        foreach ($iter as $file) {
            if ($file->isDir()) {
                continue;
            }

            $result[] = $file->getRealPath();
        }

        return $result;
    }
}
