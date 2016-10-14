<?php

namespace Busybee\ImageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response ;

class ImageController extends Controller
{

    public function displayAction($entity, $id, $width = 0, $height = 0)
    {


	 	$em = $this->getDoctrine()->getManager();

    	$entity = $em->getRepository($entity)->find($id);
		// Generate response
		$response = new Response();

		switch ($entity->getType()) {
			case 'image/jpeg':
				$extension = 'jpeg';
				$dst_imageageclass = 'imagecreatefromjpeg';
				break ;
			case 'image/png':
				$extension = 'png';
				$dst_imageageclass = 'imagecreatefrompng';
				break ;
			case 'image/gif':
				$extension = 'gif';
				$dst_imageageclass = 'imagecreatefromgif';
				break ;
			default:
				$response->setContent('Failed');
				return $response;
		}




		if (empty($width))
			$width = 200;
		if (empty($height))
			$height = 200;
			
		
		$dst_image = imagecreatetruecolor($width, $height);
		$src_image = imagecreatefromstring( stream_get_contents( $entity->getFile() ) );
		$src_w = imagesx($src_image);
		$src_h = imagesy($src_image);
		$src_x = 0;
		$src_y = 0;
		if ($src_w/$src_h > 1.33) {
			$y = intval($src_h/3);
			$w = $y * 4 ;
			$src_x = intval(($src_w - $w) / 2);
			$src_w = $w;			
		}
		if ($src_w/$src_h < 0.75) {
			$x = intval($src_w/3);
			$h = $x * 4 ;
			$src_y = intval(($src_h - $h) / 2);
			$src_h = $h;			
		}



		$factor = $src_w/$width;

		if ($src_h/$height > $factor)
			$factor = $src_h/$height;
		$dst_w = intval($src_w/$factor);
		$dst_h = intval($src_h/$factor);
		$dst_x = 0;
		$dst_y = 0;
		
		$xdif = $width - $dst_w;
		$ydif = $height - $dst_h;
		if ($xdif > 1) {
			$dst_x = intval($xdif/2);
		}
		if ($ydif > 1) {
			$dst_y = intval($ydif/2);
		}

		$background = imagecolorallocate($dst_image, 0, 0, 0);
		imagecolortransparent($dst_image, $background);
// bool imagecopyresized ( resource $dst_image , resource $src_image , int $dst_x , int $dst_y , int $src_x , int $src_y , int $dst_w , int $dst_h , int $src_w , int $src_h )

		imagecopyresized($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);

		// Set headers
		$response->headers->set('Cache-Control', 'private');
		$response->headers->set('Content-type ', 'image/png');
		$response->headers->set('Content-Disposition', 'attachment; filename="'.basename($entity->getName(), $extension).'png";');
//		$response->headers->set('Content-length', strlen($dst_imageage));

		// Send headers before outputting anything
		$response->sendHeaders();
		
		$response->setContent(imagepng( $dst_image ));    
		return $response;
    }
}
