<?php

namespace PicturesRoulette\Utils;

trait StringUtils
{
    /**
     * Human readable filesize.
     * https://stackoverflow.com/questions/15188033/human-readable-file-size
     *
     * @param int $_size_in_bytes
     *
     * @return string
     */
    public function getHumanReadableFileSize(int $_size_in_bytes): string
    {
        if ($_size_in_bytes === 0) {
            return '0.00 B';
        }

        $s = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
        $e = \floor(\log($_size_in_bytes, 1024));

        return \round($_size_in_bytes / (1024 ** $e), 2) . $s[$e];
    }

    /**
     * Получить расширение из пути к файлу.
     *
     * @param string $_path
     *
     * @return string
     */
    public function getExtensionFromPath(string $_path): string
    {
        $exp = explode('.', $_path);
        return $exp[count($exp) - 1];
    }

    /**
     * @param string $_postNumber
     * @param int    $_last_count
     *
     * @return int
     */
    public function getLastNSymbolsFromPostNumber(string $_postNumber, int $_last_count): int
    {
        return ((int)\mb_substr($_postNumber, (-1) * $_last_count));
    }

    /**
     * @param string $_str
     * @param int    $_last_count
     *
     * @return string
     */
    public function getLastNSymbolsFromString(string $_str, int $_last_count): string
    {
        return \mb_substr($_str, (-1) * $_last_count);
    }
}
