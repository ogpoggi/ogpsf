<?php
/**
 * Created by PhpStorm.
 * User: BPOGGI
 * Date: 20/02/2019
 * Time: 15:13
 */

namespace App\Helper;


class Curl {
    public static $rawdebug = false;

    public static function get($url, $options = null, &$httpcode = null) {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_USERAGENT,
            'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2623.75 Safari/537.36');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

        if (is_array($options)) {
            curl_setopt_array($ch, $options);
        }

        $result = curl_exec($ch);
        $curlerr = curl_errno($ch);
        if ($curlerr !== 0) {
            $error = curl_error($ch);
            curl_close($ch);
            $httpcode = $curlerr;
            return 'Erreur : ' . $error;
        }

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpcode > 300) {
            curl_close($ch);
            return 'Erreur : ' . $httpcode;
        }
        curl_close($ch);
        return $result;
    }

    /**
     * wrapper for the curl_* functions to make a POST request to the given url with given body.
     *
     * @param type $url
     * @param type $request_body
     * @param type $port
     * @param type $optional_headers
     * @return type
     */
    public static function do_post_request($url, $request_body, $port = false, &$optional_headers = null) {
        $ch = curl_init($url);
        if ($port !== false) {
            curl_setopt($ch, CURLOPT_PORT, $port);
        }
        curl_setopt($ch, CURLOPT_USERAGENT,
            "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/38.0.2125.104 Safari/537.36");

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request_body);

        $has_headers = !empty($optional_headers);
        curl_setopt($ch, CURLOPT_HEADER, $has_headers);
        if ($has_headers) {
            $hasCL = false;
            foreach ($optional_headers as $index => $header) {
                if (substr($header, 0, 15) == 'Content-Length:') {
                    $hasCL = true;
                    break;
                }
            }
            if (!$hasCL) {
                $cl = strlen($request_body);
                $optional_headers[] = "Content-Length: $cl";
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $optional_headers);
        }

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $result = curl_exec($ch);
        if (false === $result) {
            $errno = curl_errno($ch);
            $error = curl_error($ch);
            static::debugError($url, $optional_headers, $request_body, "$errno: $error");
            return 'Erreur :\n' . $error;
        }
        curl_close($ch);

        return $result;
    }

    public static function request($options) {
        if (is_array($options)) {
            return call_user_func(__METHOD__, (object) $options);
        }

        $url = $options->url;
        if (!empty($options->query)) {
            $query = $options->query;
            if (!is_string($query)) {
                $query = http_build_query($query);
            }
            if (\Inx\Utils::endsWith($url, '?') || \Inx\Utils::endsWith($url, '&')) {
                $url .= $query;
            } else if (strrpos($url, '?') > 0) {
                $url .= "&$query";
            } else {
                $url .= "?$query";
            }
        }
        $ch = curl_init($url);
        if (isset($options->port) && is_numeric($options->port)) {
            curl_setopt($ch, CURLOPT_PORT, $options->port);
        }
        if (isset($options->method) && is_string($options->method)) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $options->method);
        }

        if (isset($options->headers) && is_array($options->headers)) {
            $headers_snd = $options->headers;
        } else {
            $headers_snd = array();
        }

        $output_file = false;
        if (isset($options->output_file)) {
            $base = realpath(\Inx\Cache::privateDir(''));
            $file = $options->output_file;
            if (self::is_in_folder($base, $file)) {
                @mkdir(dirname($file), 0777, true);
                $output_file = fopen($file, 'w+');
                curl_setopt($ch, CURLOPT_FILE, $output_file);
            }
        }

        if ($output_file === false) {
            if (isset($options->{'body-form'})) {
                $form = $options->{'body-form'};
                if (!is_string($form)) {
                    $form = http_build_query($form);
                }
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $form);
            }

            if (isset($options->{'body-json'})) {
                $headers_snd[] = 'Content-Type: application/json';

                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($options->{'body-json'}));
            }

            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        }

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_USERAGENT,
            "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/38.0.2125.104 Safari/537.36");
        if (isset($options->CURLOPT)) {
            curl_setopt_array($ch, $options->CURLOPT);
        }
        if (!empty($headers_snd)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers_snd);
        }

        if (self::$rawdebug && \Inx\Auth::su()) {
            print_r($headers_snd);
            curl_setopt($ch, CURLOPT_VERBOSE, true);
            $verbose = fopen('php://temp', 'w+');
            curl_setopt($ch, CURLOPT_STDERR, $verbose);
        }
        $result = curl_exec($ch);
        if (self::$rawdebug && \Inx\Auth::su()) {
            print_r($result);
            rewind($verbose);
            print_r(stream_get_contents($verbose));
        }
        if (false === $result) {
            $curl_errno = curl_errno($ch);
            $curl_error = curl_error($ch);
            curl_close($ch);
            if (isset($options->retry) && $options->retry > 0) {
                $options->retry--;
                return static::request($options);
            }
            return array(
                'code'    => $curl_errno,
                'code_ok' => false,
                'error'   => $curl_error
            );
        }

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $code_ok = ($code >= 200 && $code < 400);

        curl_close($ch);

        $headers_rcv = substr($result, 0, $header_size);
        //print_r("($header_size)= $headers_rcv");
        if (!empty($headers_rcv)) {
            $headers_rcv = http_parse_headers($headers_rcv);
        }

        if ($output_file !== false) {
            fclose($output_file);
            // body downloaded to file, so stop here.
            return array(
                'code'    => $code,
                'code_ok' => $code_ok,
                'headers' => $headers_rcv,
                'url'     => $url);
        }

        $body = substr($result, $header_size);

        if (static::header($headers_rcv, 'Content-Encoding') == 'deflate') {
            $body = gzinflate($body);
        }
        if (isset($options->accept)) {
            switch ($options->accept) {
                case 'json': $body = json_decode($body, true); break;
            }
        } else {
            switch (static::header($result, 'Content-Type')) {
                case 'application/json': $body = json_decode($body, true); break;
            }
        }
        return array(
            'code'    => $code,
            'code_ok' => $code_ok,
            'body'    => $body,
            'headers' => $headers_rcv,
            'url'     => $url);
    }

    public static function header($headers, $name) {
        if (isset($headers[$name])) {
            return $headers[$name];
        }
        return false;
    }

    private static function debugError($url, $optional_headers, $request_body, $error) {
        $optional_headers = print_r($optional_headers, true);
        $message = <<<EOT
Erreur CURL sur MADRA:<br>
$url<br>
<pre>$optional_headers</pre><br>
<pre>$request_body</pre><br><hr><br>
$error
EOT;
        //\Inx\EmailAlert::sendEmail(\Inx\Mailer::SUPPORT, 'erreur MADRA', $message);
    }

}

if (!function_exists('http_parse_headers')) {

    function http_parse_headers($header) {
        $retVal = array();
        $fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $header));
        foreach ($fields as $field) {
            if (preg_match('/([^:]+): (.+)/m', $field, $match)) {
                $match[1] = ucwords(strtolower(trim($match[1])), "- \t\r\n\f\v");
                if (isset($retVal[$match[1]])) {
                    $retVal[$match[1]] = array($retVal[$match[1]], $match[2]);
                } else {
                    $retVal[$match[1]] = trim($match[2]);
                }
            }
        }
        return $retVal;
    }

}