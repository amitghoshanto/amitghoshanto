For Salman

<?php
function upload_image($run, $photo_src, $save_src, $width = 0, $height = 0, $quality = 100)
{
    $quality = (int)$quality;
    if ($quality < 0 or $quality > 100) {
        $quality = 100;
    }
    if (file_exists($photo_src)) {
        if (strrpos($photo_src, '.')) {
            $ext = substr($photo_src, strrpos($photo_src, '.') + 1, strlen($photo_src) - strrpos($photo_src, '.'));
            $fxt = (in_array($ext, array('jpeg', 'png', 'gif'))) ? $ext : "jpeg";
        } else {
            $ext = $fxt = 0;
        }
        if (preg_match('/(jpg|jpeg|png|gif)/', $ext)) {
            if ($fxt == "gif") {
                copy($photo_src, $save_src);
                return true;
            }
            list($photo_width, $photo_height) = getimagesize($photo_src);
            $create_from = "imagecreatefrom" . $fxt;
            $photo_source = $create_from($photo_src);
            if ($run == "crop") {
                if ($width > 0 && $height > 0) {
                    $crop_width = $photo_width;
                    $crop_height = $photo_height;
                    $k_w = 1;
                    $k_h = 1;
                    $dst_x = 0;
                    $dst_y = 0;
                    $src_x = 0;
                    $src_y = 0;
                    if ($width == 0 or $width > $photo_width) {
                        $width = $photo_width;
                    }
                    if ($height == 0 or $height > $photo_height) {
                        $height = $photo_height;
                    }
                    $crop_width = $width;
                    $crop_height = $height;
                    if ($crop_width > $photo_width) {
                        $dst_x = ($crop_width - $photo_width) / 2;
                    }
                    if ($crop_height > $photo_height) {
                        $dst_y = ($crop_height - $photo_height) / 2;
                    }
                    if ($crop_width < $photo_width || $crop_height < $photo_height) {
                        $k_w = $crop_width / $photo_width;
                        $k_h = $crop_height / $photo_height;
                        if ($crop_height > $photo_height) {
                            $src_x = ($photo_width - $crop_width) / 2;
                        } elseif ($crop_width > $photo_width) {
                            $src_y = ($photo_height - $crop_height) / 2;
                        } else {
                            if ($k_h > $k_w) {
                                $src_x = round(($photo_width - ($crop_width / $k_h)) / 2);
                            } else {
                                $src_y = round(($photo_height - ($crop_height / $k_w)) / 2);
                            }
                        }
                    }
                    $crop_image = @imagecreatetruecolor($crop_width, $crop_height);
                    if ($ext == "png") {
                        @imagesavealpha($crop_image, true);
                        @imagefill($crop_image, 0, 0, @imagecolorallocatealpha($crop_image, 0, 0, 0, 127));
                    }
                    @imagecopyresampled($crop_image, $photo_source, $dst_x, $dst_y, $src_x, $src_y, $crop_width - 2 * $dst_x, $crop_height - 2 * $dst_y, $photo_width - 2 * $src_x, $photo_height - 2 * $src_y);
                    @imageinterlace($crop_image, true);
                    if ($fxt == "jpeg") {
                        @imagejpeg($crop_image, $save_src, $quality);
                    } elseif ($fxt == "png") {
                        @imagepng($crop_image, $save_src);
                    } elseif ($fxt == "gif") {
                        @imagegif($crop_image, $save_src);
                    }
                    @imagedestroy($crop_image);
                }
            } elseif ($run == "resize") {
                if ($width == 0 && $height == 0) {
                    return false;
                }
                if ($width > 0 && $height == 0) {
                    $resize_width = $width;
                    $resize_ratio = $resize_width / $photo_width;
                    $resize_height = floor($photo_height * $resize_ratio);
                } elseif ($width == 0 && $height > 0) {
                    $resize_height = $height;
                    $resize_ratio = $resize_height / $photo_height;
                    $resize_width = floor($photo_width * $resize_ratio);
                } elseif ($width > 0 && $height > 0) {
                    $resize_width = $width;
                    $resize_height = $height;
                }
                if ($resize_width > 0 && $resize_height > 0) {
                    $resize_image = @imagecreatetruecolor($resize_width, $resize_height);
                    if ($ext == "png") {
                        @imagesavealpha($resize_image, true);
                        @imagefill($resize_image, 0, 0, @imagecolorallocatealpha($resize_image, 0, 0, 0, 127));
                    }
                    @imagecopyresampled($resize_image, $photo_source, 0, 0, 0, 0, $resize_width, $resize_height, $photo_width, $photo_height);
                    @imageinterlace($resize_image, true);
                    if ($fxt == "jpeg") {
                        @imagejpeg($resize_image, $save_src, $quality);
                    } elseif ($fxt == "png") {
                        @imagepng($resize_image, $save_src);
                    } elseif ($fxt == "gif") {
                        @imagegif($resize_image, $save_src);
                    }
                    @imagedestroy($resize_image);
                }
            } elseif ($run == "scale") {
                if ($width == 0) {
                    $width = 100;
                }
                if ($height == 0) {
                    $height = 100;
                }
                $scale_width = $photo_width * ($width / 100);
                $scale_height = $photo_height * ($height / 100);
                $scale_image = @imagecreatetruecolor($scale_width, $scale_height);
                if ($ext == "png") {
                    @imagesavealpha($scale_image, true);
                    @imagefill($scale_image, 0, 0, imagecolorallocatealpha($scale_image, 0, 0, 0, 127));
                }
                @imagecopyresampled($scale_image, $photo_source, 0, 0, 0, 0, $scale_width, $scale_height, $photo_width, $photo_height);
                @imageinterlace($scale_image, true);
                if ($fxt == "jpeg") {
                    @imagejpeg($scale_image, $save_src, $quality);
                } elseif ($fxt == "png") {
                    @imagepng($scale_image, $save_src);
                } elseif ($fxt == "gif") {
                    @imagegif($scale_image, $save_src);
                }
                @imagedestroy($scale_image);
            }
        }
    }
}


function get_image_mime_type($image_path)
{
    $mimes = array(IMAGETYPE_GIF => "gif", IMAGETYPE_JPEG => "jpg", IMAGETYPE_PNG => "png", IMAGETYPE_SWF => "swf", IMAGETYPE_PSD => "psd", IMAGETYPE_BMP => "bmp", IMAGETYPE_TIFF_II => "tiff", IMAGETYPE_TIFF_MM => "tiff", IMAGETYPE_JPC => "jpc", IMAGETYPE_JP2 => "jp2", IMAGETYPE_JPX => "jpx", IMAGETYPE_JB2 => "jb2", IMAGETYPE_SWC => "swc", IMAGETYPE_IFF => "iff", IMAGETYPE_WBMP => "wbmp", IMAGETYPE_XBM => "xbm", IMAGETYPE_ICO => "ico");
    if (($image_type = exif_imagetype($image_path)) && (array_key_exists($image_type, $mimes))) {
        return $mimes[$image_type];
    } else {
        return FALSE;
    }
}

function get_ext($Url)
{
    $len = strlen($Url);
    $rpos = strrpos($Url, '.');
    $begin = $len - $rpos;
    $end = $rpos + 1;
    $sub = substr($Url, $end, $begin);
    $ext = strtolower($sub);
    return $ext;
}




if (isset($_FILES['pro_pic'])) {
    $name = $_FILES['pro_pic']['name'];
    $id = date('d-m-y-h-i', time());
    $pic_ext = get_ext($name);
    $foldername = 'upload/img/' . time() . '/';
    $testdir = is_dir($foldername);
    if ($testdir != true) {
        mkdir($foldername);
    }
    $dir = $foldername . 'propic_1';
    $source = $_FILES['pro_pic']['tmp_name'];
    move_uploaded_file($source, $dir . '.' . $pic_ext);
    $source = $dir . '.' . $pic_ext;
    list($width, $height) = getimagesize($source);
    $max_width = ($width > 5000) ? 5000 : $width;
    $new_thumb1 = $dir . '_1_' . $id . '.' . $pic_ext;
    $new_thumb2 = $dir . '_2_' . $id . '.' . $pic_ext;
    $new_thumb3 = $dir . '_3_' . $id . '.' . $pic_ext;
    $new_thumb4 = $dir . '_4_' . $id . '.' . $pic_ext;
    upload_image('crop', $source, $new_thumb1, 400, 400, 90);
    upload_image('crop', $source, $new_thumb2, 600, 400, 90);
    upload_image('crop', $source, $new_thumb3, 800, 800, 90);
    upload_image('crop', $source, $new_thumb4, 600, 250, 90);
}
?>


<form action="" method="post" accept-charset="utf-8" enctype="multipart/form-data">
    <input type="file" name="pro_pic">
    <input type="submit">

</form>