<?php

namespace App\Services;

use App\Services\Base as BaseService;

class FileService extends BaseService
{
    const FILE_TYPE = ['temp', 'avatar', 'product'];
    const FILE_COMPERSS = ['jpg', 'jpeg', 'png'];
    const MAX_OFFSET = 1200;

    /**
     * 通过文件路径上传
     *
     * @param string $filePath 待上传文件路径
     * @param string $module 图片业务类别（可不传）,比如头像 avatar 商品 product
     * @return mix 失败返回 false
     */
    public function upload($file, $cate = 'temp', $prev='')
    {
        if (!in_array($cate, self::FILE_TYPE)) return false;

        $tmpname = explode('.', $file['name']);
        $extension = $tmpname[1] ?? ''; //后缀
        $tmpFile = $file['tmp_name']; //上传文件路径

        $hash = hash_file('md5', $tmpFile); //生成文件hash值

        $attachmentService = \App::make('App\Services\AttachmentService');

        $returnData = [];
        if ($attachmentService->isExitsHash($hash)) { 
            //文件已存在
            $returnData = $attachmentService->getAttachmentByHash($hash);
        } else {

            $insert = [
                'name' => $hash,
                'type' => $extension,
                'cate' => $cate,
                'source_name' => substr($tmpname[0], strrpos($tmpname[0], '/') + 1),
                'size' => $file['size'] ?? filesize($file['name']),
            ];

            //保存文件地址
            $saveUrl = getenv('FILE_CENTER').$cate;

            if (!empty($prev))
            	$saveUrl .= '/'.$prev;

            $saveUrl .= '/'.$hash.'.'.$extension;

            // dd($this->squareImage('C:\Users\LMR\Pictures\Saved Pictures\B612Kaji_20180526_105206_792.jpg'));

            file_put_contents('./1.jpeg', $this->squareImage('C:\Users\LMR\Pictures\Camera Roll\2a35655e8ab3bd0bc82e34a494e57a7d.jpg'));


            dd(4444);
            $savePath = pathinfo($saveUrl, PATHINFO_DIRNAME);

            //创建目录
            if (!is_dir($savePath)) {
                mkdir($savePath, 0777, true);
            }
            $result = move_uploaded_file($tmpFile, $saveUrl);

            if ($result) {
                //水印
                // $imageWater = new \App\Service\Utils\ImageWater($saveUrl);
                // $imageWater->output();
                //图片文件压缩存放
                if (in_array(strtolower($extension), self::FILE_COMPERSS)) {
                    $imageCompress = new \app\Services\ImageCompress($saveUrl);
                    $imageCompress->compressImg(getenv('FILE_CENTER').$cate.'/small/'.$hash.'.'.$extension);
                }

                $attachmentId = $attachmentService->create($insert);
                if (!$attachmentId) return false;
                $insert['attach_id'] = $attachmentId;
                $insert['file_url'] = str_replace('\\', '/', getenv('FILE_DOMAIN').$cate.'/small/'.$hash.'.'.$extension);
            }
            $returnData = $insert;
        }

        return $returnData;
    }

    /**
     * @method 图片填充正方形
     * @author Victoria
     * @date   2020-06-25
     * @return image/data
     */
    protected function squareImage($image)
    {
    	if (empty($image)) return $image;

		$temp = getimagesize($image);
		$width = $temp[0] ?? 0;
		$height = $temp[1] ?? 0;

		$mime = $temp['mime'] ?? '';

    	if ($width <= 0 || $height <= 0  || $width == $height) return $image;

    	$format = strtolower(preg_replace('/^.*?\//', '', $mime));
    	switch ($format) {
		    case 'jpg':
		    case 'jpeg':
		        $image_data = imagecreatefromjpeg($image);
		    break;
		    case 'png':
		        $image_data = imagecreatefrompng($image);
		    break;
		    case 'gif':
		        $image_data = imagecreatefromgif($image);
		    break;
		    default:
		        return $image;
		    break;
		}

		if ($image_data == false) return false;

		$max_offset = max($height, $width);

		$max_offset = min($max_offset, self::constant('MAX_OFFSET'));

		$bg = imagecreatetruecolor($max_offset, $max_offset);
		$white = imagecolorallocate($bg, 255,255,255);
		imagefill($bg, 0, 0, $white);//填充背景

		$x = ($max_offset - $width) / 2;
		$y = ($max_offset - $height) / 2;

		dd($x);

		//创建画布
    	imagecopymerge($bg, $image_data, $x, $y, 0, 0, $width, $height, $max_offset);

	    switch( $format) {
	        case 'jpg':
	        case 'jpeg':
	            imagejpeg($bg, $image, $max_offset);
	        break;
	        case 'png':
	            imagepng($bg, $image);
	        break;
	        case 'gif':
	            imagegif($bg, $image);
	        break;
	        default:
	            return $image;
	        break;
	    }

		imagedestroy($bg);
		imagedestroy($image_data);

		return $image;
    }
}
