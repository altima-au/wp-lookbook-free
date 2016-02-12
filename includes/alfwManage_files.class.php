<?php
    require_once('alfwTranslit.php');

/**
 * Files manipulation class
 */
class alfw_manage_files {

    /**
     * modify file name for unique
     */
    function modif_file_name($path, $filename)
    {
        if (is_file($path . $filename)) {
            if (preg_match("/^(\d+)_(.+)$/is", $filename, $res)) {
                $main_name = $res[2];
                $digit_name = $res[1];
                $digit_name++;
                $filename = $digit_name . "_" . $main_name;
            } else {
                $filename = "0_" . $filename;
            }
            $filename = $this->modif_file_name($path, $filename);
        }
        return $filename;
    }

    /**
     * @param $file_name
     */
    function safe_file_name(&$file_name) {
        if (preg_match("/[а-яА-Я]+/si", $file_name)) {
            $file_name = alfw_Translit::encodestring($file_name);
        }

        $file_name = preg_replace("/[^0-9a-zA-z\._\-\S]/is", "_", $file_name);
    }

    /**
     * create folders structure
     */
    function create_folder_recursive($path, $permissions = 0775)
    {
        $path_part = array();
        $path_part = explode("/", $path);
        $test_path = "";
        foreach ($path_part as $dir) {
            $test_path .= $dir . "/";
            if (($test_path != "./") || ($test_path != "../")) {
                if (!is_dir($test_path)) {
                    mkdir($test_path, $permissions);
                }
            }

        }
    }

    /**
     * Upload file to server
     * @param $file
     * @param $path
     * @param $file_name
     * @param bool $safe
     * @return bool|string
     * @throws Exception
     */
    function upload_file($file, $path, $file_name, $safe = true) {

        $this->safe_file_name($file_name);

        if ($safe)
            $file_name = $this->modif_file_name($path, $file_name);

        if (is_dir($path)) {
            umask(0002);
            if (is_uploaded_file($file)) {
                if (!move_uploaded_file($file, $path . $file_name))
                    throw new Exception('File can\'t copyed to destination folder!');
            } else {
                return false;
            }
        } else {
            $this->create_folder_recursive($path);
            //mkdir($path,0775);
            umask(0002);
            if (is_uploaded_file($file)) {
                if (!move_uploaded_file($file, $path . $file_name))
                    throw new Exception('File can\'t copyed to destination folder!');
            } else {
                return false;
            }
        }
        return $file_name;
    }

    /**
     * @param $file
     * @param $path
     * @param $file_name
     * @param bool $safe
     * @return bool|string
     * @throws Exception
     */
    function copy_file($file, $path, $file_name, $safe = true) {

        $this->safe_file_name($file_name);

        if ($safe)
            $file_name = $this->modif_file_name($path, $file_name);

        if (is_dir($path)) {
            umask(0002);
            if (is_file($file)) {
                if (!copy($file, $path . $file_name))
                    throw new Exception('File can\'t copyed to destination folder!');
            } else {
                return false;
            }
        } else {
            $this->create_folder_recursive($path);
            umask(0002);
            if (is_file($file)) {
                if (!copy($file, $path . $file_name))
                    throw new Exception('File can\'t copyed to destination folder!');
            } else {
                return false;
            }
        }
        return $file_name;
    }

    /**
     * get file list for defined folder
     */
    function enum_directory_files($path, $file_ext = array())
    {
        $files = array();
        if (!file_exists($path))
            return $files;
        if (($dir = @opendir($path)) !== false) {
            $i = 0;
            while (($file = readdir($dir)) !== false) {
                if (!is_dir($path . "/" . $file)) {
                    if (count($file_ext)) {
                        foreach ($file_ext as $ext) {
                            if (preg_match("/([\w\.-_а-я]+)($ext$)/is", $file))
                                $files[$i++] = $file;

                        }
                    } else {
                        $files[$i++] = $file;
                    }
                }
            }
            closedir($dir);
        }
        return $files;
    }

    /**
     * Delete folder (if even not empty)
     */
    function delete_directory($path)
    {
        if (!file_exists($path))
            return;
        if (($dir = @opendir($path)) !== false) {
            while (($file = readdir($dir)) !== false) {
                $file_path = $path . "/" . $file;
                if (is_dir($file_path)) {
                    if ($file != "." && $file != "..") {
                        $this->delete_directory($file_path);
                    }
                } else {
                    unlink($file_path);
                }
            }
            closedir($dir);
            rmdir($path);
        }
    }

    /**
     * Delete File
     */
    function delete_file($path_to_file)
    {
        if (file_exists($path_to_file) && (is_file($path_to_file))) {
            return unlink($path_to_file);
        } else {
            return false;
        }
    }


    /**
     * ресайзит картинку и загружает на сервер
     * $picture - данные
     * $picture_name - имя файла
     * $new_height="", $new_width="" - восота и ширина соответственно, может быть задано что-то одно
     * $path - путь куда положить результат,
     * $force = true - если правда то картинки даже с меньшими размерами чем задаваемые будут менять свой размер
     * $unique_file_name - если правда то при существовании файла с тем же именем будет создан файл с новым именем, иначе перезаписан
     */
    function resize_upload_img($picture, $picture_name, $new_height = "", $new_width = "", $path, $force = false, $unique_file_name = true)
    {
        $width_f = false;
        $height_f = false;

        if (!is_dir($path)) {
            $this->create_folder_recursive($path);
        }

        $this->safe_file_name($picture_name);

        if ($unique_file_name)
            $picture_name = $this->modif_file_name($path, $picture_name);

        $koef = 0;
        list($old_width, $old_height, $type, $attr) = getimagesize($picture);

        $force_upload = false;

        if (!$force) {

            if (!empty($new_height) && !empty($new_width)) {
                if (($old_width <= $new_width) && ($old_height <= $new_height)) {
                    if (!copy($picture, $path . $picture_name))
                        throw new Exception('File can\'t copyed to destination folder!');
                    $force_upload = true;
                }
            } elseif (!empty($new_height)) {
                if ($old_height <= $new_height) {
                    if (!copy($picture, $path . $picture_name))
                        throw new Exception('File can\'t copyed to destination folder!');
                    $force_upload = true;
                }
            } else {
                if ($old_width <= $new_width) {
                    if (!copy($picture, $path . $picture_name))
                        throw new Exception('File can\'t copyed to destination folder!');
                    $force_upload = true;
                }
            }
        }

        if ($force_upload)
            return $picture_name;

        switch ($type) {
            case 1:
                //gif
                $old_img = imagecreatefromgif($picture);
                break;
            case 2:
                //jpg
                $old_img = imagecreatefromjpeg($picture);
                break;
            case 3:
                //png
                $old_img = imagecreatefrompng($picture);
                break;
            default:
                return false;
        }

        if ($old_width > $old_height) {
            $koef = $old_width / $old_height;
            $width_f = true;
        } else {
            $koef = $old_height / $old_width;
            $height_f = true;
        }

        if (empty($new_height)) {
            if ($height_f)
                $new_height = $new_width * $koef;
            else
                $new_height = $new_width / $koef;
        } elseif (empty($new_width)) {
            if ($width_f)
                $new_width = $new_height * $koef;
            else
                $new_width = $new_height / $koef;
        }

        $new_img = imageCreateTrueColor($new_width, $new_height);

        imagecopyresampled($new_img, $old_img, 0, 0, 0, 0, $new_width, $new_height, $old_width, $old_height);

        $res = false;

        switch ($type) {
            case 1:
                //gif
                $res = imagegif($new_img, $path . $picture_name);
                break;
            case 2:
                //jpg
                $res = imagejpeg($new_img, $path . $picture_name);
                break;
            case 3:
                //png
                $res = imagepng($new_img, $path . $picture_name);
                break;
        }

        imagedestroy($new_img);
        imagedestroy($old_img);

        if (!$res) {
            throw new Exception('File not uploaded!');
        }
        return $picture_name;
    }

    /**
     * ресайзит картинку
     * $picture - путь к картинке,
     * $picture_name -имя картинки,
     * $new_height - новая высота,
     * $new_width - новая ширина,
     * $auto_define_orient - если задан этот флаг то выбирается одно из заданных $new_height или $new_width
     */
    function resize_img_on_fly($picture, $picture_name, $new_height = 0, $new_width = 0, $auto_define_orient = true)
    {
        $width_f = false;
        $height_f = false;

        $koef = 0;
        list($old_width, $old_height, $type, $attr) = getimagesize($picture);

        if ($auto_define_orient) {
            if ($old_width > $old_height) {
                if (empty($new_width))
                    $new_width = $new_height;

                $new_height = 0;
            } else {
                if (empty($new_height))
                    $new_height = $new_width;
                $new_width = 0;
            }
        }
        switch ($type) {
            case 1:
                //gif
                $old_img = imagecreatefromgif($picture);
                break;
            case 2:
                //jpg
                $old_img = imagecreatefromjpeg($picture);
                break;
            case 3:
                //png
                $old_img = imagecreatefrompng($picture);
                break;
            default:
                return false;
        }

        if ($old_width > $old_height) {
            $koef = $old_width / $old_height;
            $width_f = true;
        } else {
            $koef = $old_height / $old_width;
            $height_f = true;
        }

        if (empty($new_height)) {
            if ($height_f) {
                $new_height = $new_width * $koef;
            } else {
                $new_height = $new_width / $koef;
            }
        } elseif (empty($new_width)) {
            if ($width_f) {
                $new_width = $new_height * $koef;
            } else {
                $new_width = $new_height / $koef;
            }
        }

        $new_img = imageCreateTrueColor($new_width, $new_height);
        imagecopyresampled($new_img, $old_img, 0, 0, 0, 0, $new_width, $new_height, $old_width, $old_height);

        switch ($type) {
            case 1:
                //gif
                header('Content-type: image/gif');
                imagegif($new_img);
                break;
            case 2:
                //jpg
                header('Content-type: image/jpeg');
                imagejpeg($new_img);
                break;
            case 3:
                //png
                header('Content-type: image/png');
                imagepng($new_img);
                break;
        }

        imagedestroy($new_img);
        imagedestroy($old_img);
    }

    /**
     * crop image
     */
    function crop_image($src_img, $dst_img, $x, $y, $xx, $yy, $w, $h, $path_to_tmp = "../tmp/")
    {

        $img = NULL;
        $img_width = NULL;
        $img_height = NULL;
        $type = NULL;
        $attr = NULL;

        list($img_width, $img_height, $type, $attr) = getimagesize($src_img);

        switch ($type) {
            case 1:
                //gif
                $img = imagecreatefromgif($src_img);
                break;
            case 2:
                //jpg
                $img = imagecreatefromjpeg($src_img);
                break;
            case 3:
                //png
                $img = imagecreatefrompng($src_img);
                break;
            default:
                return false;
        }

        $crop = imagecreatetruecolor($w, $h);
        imagecopy($crop, $img, $x, $y, $xx, $yy, $w, $h);

        switch ($type) {
            case 1:
                //gif
                imagegif($crop, $path_to_tmp . $dst_img);
                break;
            case 2:
                //jpg
                imagejpeg($crop, $path_to_tmp . $dst_img);
                break;
            case 3:
                //png
                imagepng($crop, $path_to_tmp . $dst_img);
                break;
        }

        imagedestroy($img);
        imagedestroy($crop);

        return $dst_img;
    }

    /**
     * add to image text (ttf)
     */
    function write_text_to_picture($image, $image_name, $fontfile, $size = 10, $angle = 0, $x = 5, $y = 5, $color = "FF0000", $text = "Test...", $result_path = "../tmp/")
    {

        $result = NULL;
        list($width, $height, $type, $attr) = getimagesize($image);

        switch ($type) {
            case 1:
                //gif
                $img = imagecreatefromgif($image);
                break;
            case 2:
                //jpg
                $img = imagecreatefromjpeg($image);
                break;
            case 3:
                //png
                $img = imagecreatefrompng($image);
                break;
            default:
                return false;
        }

        preg_match_all('/\w{2}/si', strtolower($color), $matched);
        $red = hexdec($matched[0][0]);
        $green = hexdec($matched[0][1]);
        $blue = hexdec($matched[0][2]);
        $textcolor = imagecolorallocate($img, $red, $green, $blue);
        imagettftext($img, $size, $angle, $x, $y, $textcolor, $fontfile, $text);

        switch ($type) {
            case 1:
                //gif
                $result = imagegif($img, $result_path . $image_name);
                break;
            case 2:
                //jpg
                $result = imagejpeg($img, $result_path . $image_name);
                break;
            case 3:
                //png
                $result = imagepng($img, $result_path . $image_name);
                break;
        }

        imagedestroy($img);

        return ($result) ? $result_path . $image_name : $result;
    }

    /**
     * @param $path
     * @throws Exception
     */
    function force_download_csv($path)
    {

        if (preg_match("/.*?([^\/]+)$/i", $path, $match)) {
            $name = $match[1]; // file name
        }

        if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE")) {
            $name = preg_replace('/\./', '%2e', $name, substr_count($name, '.') - 1);
        }


        if (!$fdl = @fopen($path, 'r')) {
            throw new Exception('Can\'t Open File!');
        } else {
            header("Cache-Control: "); // leave blank to avoid IE errors
            header("Pragma: "); // leave blank to avoid IE errors
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=\"" . $name . "\"");
            header("Content-length:" . (string)(filesize($path)));
            sleep(1);
            fpassthru($fdl);
            //exit;
        }
    }

    /**
     * @param int $frame_width in px
     * @param int $frame_height in px
     * @param string $path_picture path to picture
     * @return array
     */
    function check_orientation($frame_width, $frame_height, $path_picture) {

        $result = array();

        list($img_width, $img_height, $type, $attr) = getimagesize($path_picture);

        $frame_orientation = ($frame_width > $frame_height) ? 'horizont' : 'vertical';
        $image_orientation = ($img_width > $img_height) ? 'horizont' : 'vertical';

        if ($frame_orientation == $image_orientation) {

            $result['check'] = true;

        }else {

            $result['check'] = false;

            if ($frame_orientation == 'horizont'){
                $result['resize_width'] = '';
                $result['resize_height'] = $frame_height;
                $result['frame_orient'] = $frame_orientation;
                $result['img_orient'] = $image_orientation;
            }else{
                $result['resize_width'] = $frame_width;
                $result['resize_height'] = '';
                $result['frame_orient'] = $frame_orientation;
                $result['img_orient'] = $image_orientation;
            }
        }

        return $result;
    }

    /**
     * @param int $canvas_width
     * @param int $canvas_height
     * @param string $dst_orientation - horizont | vertical
     * @param string $picture_path
     * @return bool
     */
    function center_place_img_to_canvas($canvas_width, $canvas_height, $dst_orientation, $picture_path) {

        list($img_width, $img_height, $type, $attr) = getimagesize($picture_path);

        switch ($type) {
            case 1:
                //gif
                $src_img = imagecreatefromgif($picture_path);
                break;
            case 2:
                //jpg
                $src_img = imagecreatefromjpeg($picture_path);
                break;
            case 3:
                //png
                $src_img = imagecreatefrompng($picture_path);
                break;
            default:
                return false;
        }

        $dst_img = imagecreatetruecolor($canvas_width, $canvas_height);
        $white_color = imagecolorallocate($dst_img, 255, 255, 255);
        imagefill($dst_img, 0, 0, $white_color);

        if ($dst_orientation == 'horizont'){
            $y = 0;
            $x = ($canvas_width / 2) - ($img_width / 2 );
        }else {
            $x = 0;
            $y = ($canvas_height / 2) - ($img_height / 2 );
        }

        imagecopy($dst_img, $src_img, $x, $y, 0,0,$img_width, $img_height);

        switch ($type) {
            case 1:
                //gif
                $res = imagegif($dst_img, $picture_path);
                break;
            case 2:
                //jpg
                $res = imagejpeg($dst_img, $picture_path);
                break;
            case 3:
                //png
                $res = imagepng($dst_img, $picture_path);
                break;
        }

        imagedestroy($src_img);
        imagedestroy($dst_img);

        if (!$res) {
            throw new Exception('File not uploaded!');
        }
    }
}
