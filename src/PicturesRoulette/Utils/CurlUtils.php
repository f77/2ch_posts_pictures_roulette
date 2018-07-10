<?php

namespace PicturesRoulette\Utils;

trait CurlUtils
{

    /**
     * Выполнить HTTP-запрос при помощи CURL.
     * 
     * @param   string      $_url           URL запроса.
     * @param   array|NULL  $_postfields    Постфилды для POST-запроса. Если NULL, то будет GET-запрос.
     * @param   array       $_extra_options Дополнительные опции запроса.
     * @return string                       Ответ сервера.
     */
    protected function curlQuery (string $_url, array $_postfields = NULL, array $_extra_options = array ()): string
    {
        $ch      = \curl_init ();
        $options = [\CURLOPT_URL => $_url] + $this->getDefaultCurlOptions ();

        if ($_postfields !== NULL)
        {
            $options[\CURLOPT_POST]       = 1;
            $options[\CURLOPT_POSTFIELDS] = $_postfields;
        }


        \curl_setopt_array ($ch, ($_extra_options + $options));
        $result = curl_exec ($ch);
        \curl_close ($ch);

        return $result;
    }

    /**
     * Скачать файл при помощи CURL.
     * 
     * @param   string  $_url               URL файла.
     * @param   string  $_output_filename   Имя файла, в который будем сохранять.
     * @return  bool                        Удачно ли скачан файл?
     */
    protected function curlDownloadFile (string $_url, string $_output_filename): bool
    {
        $ch      = \curl_init ();
        $options = [\CURLOPT_URL => $_url] + $this->getDefaultCurlOptions ();
        \curl_setopt_array ($ch, $options);
        $result  = curl_exec ($ch);

        $httpCode = \curl_getinfo ($ch, \CURLINFO_HTTP_CODE);
        if ($httpCode != 200)
        {
            \curl_close ($ch);
            return FALSE;
        }

        $fp = \fopen ($_output_filename, 'wb');
        \fwrite ($fp, $result);
        \fclose ($fp);

        \curl_close ($ch);
        return TRUE;
    }

    protected function getDefaultCurlOptions (): array
    {
        return [
            \CURLOPT_RETURNTRANSFER => 1,
            \CURLOPT_FOLLOWLOCATION => 1,
            \CURLOPT_SSL_VERIFYPEER => 0,
            \CURLOPT_SSL_VERIFYHOST => 0,
            \CURLOPT_CONNECTTIMEOUT => 10,
            \CURLOPT_TIMEOUT        => 10,
        ];
    }

}
