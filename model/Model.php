<?php

class Model
{
    private $url;
    private $data = [];
    private $getData = [];
    private $test_url;

    public function __construct($url)
    {
        if($url){
            $this->getData = $this->getData($url);
            $this->url = $url;
        }

    }

    public function getReport()
    {
        $this->generationReport();
        return $this->data;
    }

    public function getData($url)
    {
        $url_str = explode(':', $url);
        $protocol = $url_str[0];

        switch ($protocol) {
            case 'http':
            case 'https': {
                return $this->getRobots($url, $protocol, null);
                break;
            }
            default: {
                return $this->getRobotsDefault($url);
                break;
            }
        }
    }

    private function getRobotsDefault($url){
        $protocols = ["www.", "http://", "https://"];
        if ($url) {
            $new_url = preg_replace('/^www./', '', $url);
            for ($i = 0; $i < count($protocols); $i++) {
                $str_url = $protocols[$i] . $new_url . '/robots.txt';
                $this->test_url = $str_url;
                $ch = curl_init($str_url);
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HEADER, 1);
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $file_info = curl_getinfo($ch);
                curl_close($ch);
                if ($httpCode && $httpCode != 301) {
                    $res = $response;
                    $info = array(
                        "http_code" => $httpCode,
                        "file_size" => $file_info['size_download'],
                        "file_content" => $res
                    );
                    return $info;
                    break;
                } else {
                    if ($httpCode == 301) {
                        continue;
                    } else {
                        if ($i == count($protocols)-1 ) {
                            $info = array(
                                "http_code" => $httpCode,
                                "file_size" => '',
                                "file_content" => ''
                            );
                            return $info;
                        } else {
                            continue;
                        }
                    }
                }
            }
        }
    }

    private function getRobots($url, $protocol=null, $call = null)
    {
        $str_url = $url . '/robots.txt';
        if (!$call) {
            $str_url = $url . '/robots.txt';
        }
        $this->test_url = $str_url;
        $ch = curl_init($str_url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $file_info = curl_getinfo($ch);
        curl_close($ch);
        if ($httpCode && $httpCode != 301) {
            $res = $response;
            $info = array(
                "http_code" => $httpCode,
                "file_size" => $file_info['size_download'],
                "file_content" => $res
            );
            return $info;
        } elseif (!$httpCode || $httpCode == 301 && !$call) {
            $replace = $protocol == 'http' ? 'https' : 'http';

            $res = str_replace($protocol, $replace, $url);

            $get_file = $this->getRobots($res, null, true);

            $info = array(
                "http_code" => $httpCode,
                "file_size" => $file_info['size_download'],
                "file_content" => $get_file
            );

            return $info['file_content'];

        } elseif (!$httpCode && $call) {
            $info = array(
                "http_code" => '',
                "file_size" => '',
                "file_content" => ''
            );
            return $info;
        }
    }

   private function checkDirective($data, $derective) {
        $str_words = explode(" ", $data);
        $cnt=0;
        for($i = 0; $i < count($str_words); $i++) {
            if(preg_match("/".$derective."/", $str_words[$i])) {
                $cnt++;
            }
        }
        return $cnt;
    }

    private function checkingRobotsFile(){
        $name_report = 'Проверка наличия файла robots.txt';
        $status_report = $this->getData['http_code'] == '200' ? 'OK' : 'Ошибка';
        if($status_report == 'OK'){
            $condition = 'Файл robots.txt присутствует';
            $recommendations = 'Доработки не требуются';
        }else{
            $condition = 'Файл robots.txt не найден';
            $recommendations = 'Программист: Создать файл robots.txt и разместить его на сайте.';
        }

        $report = [
            'name_report' => $name_report,
            'status_report' => $status_report,
            'condition' => $condition,
            'recommendations' => $recommendations
        ];

        array_push($this->data, $report);
    }

    private function verifyHostDirective(){
        $name_report = 'Проверка указания директивы Host';
        $res = $this->checkDirective($this->getData['file_content'], "Host:");

        if($res){
            $status_report = 'OK';
            $condition = 'Директива Host указана';
            $recommendations = 'Доработки не требуются';
        }elseif (!$this->getData['file_content']){
            $status_report = "Ошибка";
            $condition = 'Файл robots.txt не найден';
            $recommendations = 'Программист: Добавить файл robots.txt, затем добавить дерективу Host.';
        }
        else{
            $status_report = "Ошибка";
            $condition = 'В файле robots.txt не указана директива Host';
            $recommendations = 'Программист: Для того, чтобы поисковые системы знали, 
            какая версия сайта является основным зеркалом, необходимо прописать адрес основного зеркала в директиве Host. 
            В данный момент это не прописано. Необходимо добавить в файл robots.txt директиву Host. 
            Директива Host задётся в файле 1 раз, после всех правил.';
        }

        $report = [
            'name_report' => $name_report,
            'status_report' => $status_report,
            'condition' => $condition,
            'recommendations' => $recommendations
        ];

        array_push($this->data, $report);
    }

    private function verifyCountHosts(){
        $name_report = 'Проверка количества директив Host, прописанных в файле';
        $res = $this->checkDirective($this->getData['file_content'], "Host:");

        if($res == 1){
            $status_report = 'OK';
            $condition = 'В файле прописана 1 директива Host';
            $recommendations = 'Доработки не требуются';
        }elseif($res > 1){
            $status_report = "Ошибка";
            $condition = 'В файле прописано несколько директив Host';
            $recommendations = 'Программист: Директива Host должна быть указана в файле только 1 раз. 
            Необходимо удалить все дополнительные директивы Host и оставить только 1, 
            корректную и соответствующую основному зеркалу сайта';
        }elseif (!$this->getData['file_content']){
            $status_report = "Ошибка";
            $condition = 'Файл robots.txt не найден';
            $recommendations = 'Программист: Добавить файл robots.txt прописать дерективу Host, она должна быть указана в файле 1 раз.';
        }
        else{
            $status_report = "Ошибка";
            $condition = 'В файле не прописана директива Host';
            $recommendations = 'Программист: Директива Host должна быть указана в файле 1 раз.';
        }

        $report = [
            'name_report' => $name_report,
            'status_report' => $status_report,
            'condition' => $condition,
            'recommendations' => $recommendations
        ];

        array_push($this->data, $report);

    }

    private function verifySizeFile(){
        $name_report = 'Проверка размера файла robots.txt';
        $max_size = 32000;
        $size = (int)$this->getData['file_size'];


        if($size && $size <= $max_size && $this->getData['http_code'] == 200){
            $convert_size = $size > 1024 ? round((int)$size/1024, 1, PHP_ROUND_HALF_UP).' кб' : $size.' байт';
            $status_report = 'OK';
            $condition = 'Размер файла robots.txt составляет '.$convert_size.', что находится в пределах допустимой нормы';
            $recommendations = 'Доработки не требуются';
        }elseif (!$size || $this->getData['http_code'] != 200){
            $status_report = "Ошибка";
            $condition = 'Файл robots.txt не найден';
            $recommendations = 'Программист: Необходимо добавить файл robots.txt размером до 32 кб';
        }
        elseif ($size > $max_size){
            $convert_size = round((int)$size/1024, 1, PHP_ROUND_HALF_UP).' кб';
            $status_report = "Ошибка";
            $condition = 'Размера файла robots.txt составляет '.$convert_size.', что превышает допустимую норму';
            $recommendations = 'Программист: Максимально допустимый размер файла robots.txt составляем 32 кб. 
            Необходимо отредактировть файл robots.txt таким образом, чтобы его размер не превышал 32 Кб';
        }

        $report = [
            'name_report' => $name_report,
            'status_report' => $status_report,
            'condition' => $condition,
            'recommendations' => $recommendations
        ];

        array_push($this->data, $report);

    }

    private function verifySitemapDirective(){
        $name_report = 'Проверка указания директивы Sitemap';
        $res = $this->checkDirective($this->getData['file_content'], "Sitemap:");

        if($res){
            $status_report = 'OK';
            $condition = 'Директива Sitemap указана';
            $recommendations = 'Доработки не требуются';
        }elseif (!$this->getData['file_content']){
            $status_report = "Ошибка";
            $condition = 'Файл robots.txt не найден';
            $recommendations = 'Программист: Добавить файл robots.txt и прописать директиву Sitemap';
        }
        else{
            $status_report = "Ошибка";
            $condition = 'В файле robots.txt не указана директива Sitemap';
            $recommendations = 'Программист: Добавить в файл robots.txt директиву Sitemap';
        }

        $report = [
            'name_report' => $name_report,
            'status_report' => $status_report,
            'condition' => $condition,
            'recommendations' => $recommendations
        ];

        array_push($this->data, $report);
    }

    private function checkingResponseFile(){
        $name_report = 'Проверка кода ответа сервера для файла robots.txt';
        $http_code = $this->getData['http_code'];

        if($http_code == 200){
            $status_report = 'OK';
            $condition = 'Файл robots.txt отдаёт код ответа сервера '.$http_code;
            $recommendations = 'Доработки не требуются';
        }else{
            $status_report = "Ошибка";
            $condition = 'При обращении к файлу robots.txt сервер возвращает код ответа '.$http_code;
            $recommendations = 'Программист: Файл robots.txt должен отдавать код ответа 200, иначе файл не будет обрабатываться. 
            Необходимо настроить сайт таким образом, чтобы при обращении к файлу robots.txt сервер возвращает код ответа 200';
        }

        $report = [
            'name_report' => $name_report,
            'status_report' => $status_report,
            'condition' => $condition,
            'recommendations' => $recommendations
        ];

        array_push($this->data, $report);

    }

    private function generationReport(){
        $this->checkingRobotsFile();
        $this->verifyHostDirective();
        $this->verifyCountHosts();
        $this->verifySizeFile();
        $this->verifySitemapDirective();
        $this->checkingResponseFile();
    }

}