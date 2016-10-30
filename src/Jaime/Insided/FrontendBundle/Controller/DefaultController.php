<?php

namespace Jaime\Insided\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Jaime\Insided\FrontendBundle\Entity\Post;
use Jaime\Insided\FrontendBundle\Entity\Visita;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
	/**
	 * This function loads the index view. This is the main view of the application
	 */
    public function indexAction()
    {
    	$em = $this->getDoctrine()->getManager();
    	
    	//aumentamos el contador de visitas a la página
    	try {
    		$visita = $em->getRepository('JIFrontendBundle:Visita')->find(1);
	    	if(!$visita){
	    		$visita = new Visita();
	    		$nuevoContador = 1;
	    	}
	    	else{
	    		$contadorActual = $visita->getContador();
	    		$nuevoContador = $contadorActual + 1;
	    	}
	    	$visita->setContador($nuevoContador);
	    	
	    	$em->persist($visita);
	    	$em->flush();
    	}
    	catch (\Exception $e)
    	{
    		throw $e;//throw the exception and redirect to the custom error page located in app/Resources/TwigBundle/views/Exception/error.html.twig
    	}
    	
    	
        return $this->render('JIFrontendBundle:Default:index.html.twig', array(
        		'contador' => $nuevoContador
        ));
    }
    
    /**
     * This function loads the list of posts from the bbdd and return them via json
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function loadPostsAction()
    {
    	try {
    		$em = $this->getDoctrine()->getManager();
    		$postData = $em->getRepository('JIFrontendBundle:Post')->findPostOrderedByFecha();
	    	
	    	$ruta_imagenes_posts = $this->getRequest()->getUriForPath('/images_posts/');
	    	$ruta_imagenes_posts = str_replace("app_dev.php/", "", $ruta_imagenes_posts);
	    	
	    	foreach ($postData AS $post){
	    		$post->setPathImage($ruta_imagenes_posts.$post->getPathImage());
	    	}
	    	
	    	$posts = array("code_error"=>0, "posts"=>$postData);
	    	
	    	$serializer = $this->get('serializer');
	    	$postsJson = $serializer->serialize(
	    			$posts,
	    			'json', array('groups' => array('group1'))
	    			);
    	}
    	catch (\Exception $e)
    	{
    		$posts = array("code_error"=>1);
    		$postsJson = json_encode($posts);
    	}
    	
    	return new JsonResponse(json_decode($postsJson, true));
    }
    
    /**
     * This function loads the post, whose id is passed as parameter, and return it via json
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function loadPostAction($id)
    {
    	try {
    		$em = $this->getDoctrine()->getManager();
    		
    		$ruta_imagenes_posts = $this->getRequest()->getUriForPath('/images_posts/');
    		$ruta_imagenes_posts = str_replace("app_dev.php/", "", $ruta_imagenes_posts);
    		
    		$postData = $em->getRepository('JIFrontendBundle:Post')->find($id);
    		if($postData){
    			$postData->setPathImage($ruta_imagenes_posts.$postData->getPathImage());
    		}
    		
    		
    		$post = array("code_error"=>0, "post"=>$postData);
    	
    		$serializer = $this->get('serializer');
    		$postJson = $serializer->serialize(
    				$post,
    				'json', array('groups' => array('group1'))
    				);
    	}
    	catch (\Exception $e)
    	{
    		$post = array("code_error"=>1);
    		$postJson = json_encode($post);
    	}
    	 
    	return new JsonResponse(json_decode($postJson, true));
    }
    
    /**
     * this function loads the actual counter of views and return it via json
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function loadCounterAction()
    {
    	try {
    		$em = $this->getDoctrine()->getManager();
    	
	    	$visita = $em->getRepository('JIFrontendBundle:Visita')->find(1);
	    	$contadorActual = array("code_error"=>0, "contador" => $visita->getContador());
    	}
    	catch (\Exception $e)
    	{
    		$contadorActual = array("code_error"=>1);
    	}
    	 
    	return new JsonResponse($contadorActual);
    }
    
    /**
     * This function add a new post in the bbdd if the image is correctly loaded
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function addPostAction() {
    
    	try {
    		$request = $this->getRequest();
    		$em = $this->getDoctrine()->getManager();
    		
    		$title_new_post = $request->get('title_new_post');
    		$titulo_filtrado = ereg_replace("[^A-Za-z0-9]", "", $title_new_post);
    		
    		$newPost = new Post();
    		$newPost->setTitle($titulo_filtrado);
    		$newPost->setDate(new \DateTime());
    		$newPost->setPathImage("");
    		$em->persist($newPost);
    		$em->flush();
    		 
    		$id_post_creado = $newPost->getId();
    		 
    		foreach ($_FILES as $key) {
    		
    			if($key['error'] == UPLOAD_ERR_OK ){
    				 
    				$nombre = $key['name'];
    				$nombre = strtolower($nombre);
    				 
    				$array_datos_nombre = explode(".", $nombre);
    				$extension_archivo = $array_datos_nombre[count($array_datos_nombre)-1];
    				$extension_archivo = strtolower($extension_archivo);
    				 
    				$nombre_imagen = $id_post_creado.".".$extension_archivo;
    				$temporal = $key['tmp_name'];
    				$ruta_imagenes = __DIR__.'/../../../../../web/images_posts/';
    				 
    				$subida = move_uploaded_file($temporal, $ruta_imagenes . $nombre_imagen);
    				 
    				if($subida){
    					$newPost->setPathImage($nombre_imagen);
    					$em->persist($newPost);
    					$em->flush();
    					
    					$ruta_imagenes_posts = $this->getRequest()->getUriForPath('/images_posts/');
    					$ruta_imagenes_posts = str_replace("app_dev.php/", "", $ruta_imagenes_posts);
    					$newPost->setPathImage($ruta_imagenes_posts.$nombre_imagen);
    		
    					$serializer = $this->get('serializer');
    					$newPostJson = $serializer->serialize(
    							$newPost,
    							'json', array('groups' => array('group1'))
    							);
    		
    					$mensaje = 'The post was created correctly';
    					return new JsonResponse(array('code_error' => 0, 'mensaje' => $mensaje, 'postCreated' => json_decode($newPostJson, true)));
    				}
    			}
    			else{
    				$em->remove($newPost);
    				$em->flush();
    			}
    		}
    	}
    	catch (\Exception $e)
    	{
    	}
    	
    	
    	$mensaje = 'The post was not created ';
    	return new JsonResponse(array('code_error' => 1, 'mensaje' => $mensaje));
    }
    
    /**
     * This function create a zip and put a .cvs file, a .xls file and all images of the posts inside
     */
    public function exportPostsAction() {
    	try {
    		$em = $this->getDoctrine()->getManager();
    		 
    		$posts = $em->getRepository('JIFrontendBundle:Post')->findPostOrderedByFecha();
    		 
    		$currentTime = time();
    		 
    		//WE CREATE THE ZIP FILE
    		$zip = new \ZipArchive();
    		$zipname =  $this->get('kernel')->getRootDir() . "/../web/$currentTime.zip";
    		 
    		if ($zip->open($zipname, \ZipArchive::CREATE)!==TRUE) {
    			exit("cannot open <$zipname>\n");
    		}
    		 
    		 
    		//WE PUT THE IMAGES INTO THE ZIP FILE
    		$ruta_imagenes = __DIR__.'/../../../../../web/images_posts/';
    		$dir = opendir($ruta_imagenes);
    		while ($current = readdir($dir)){
    			if( $current != "." && $current != "..") {
    				$pathFile = $ruta_imagenes.$current;
    				$zip->addFile( $pathFile, pathinfo( $pathFile, PATHINFO_BASENAME ) );
    			}
    		}
    		 
    		//WE GENERATE THE EXCEL FILE
    		$postXlsx = $currentTime.".xls";
    		$ruta_temp = __DIR__.'/../../../../../web/temp/';
    		$rutaPostXlsx = $ruta_temp.$postXlsx;
    		
    		$response = new Response();
    		$objPHPExcel = new \PHPExcel();
    		$objPHPExcel->getProperties()->setCreator("Jaime")
    		->setLastModifiedBy("Jaime")
    		->setTitle("Post")
    		->setSubject("Post");
    		$objPHPExcel->setActiveSheetIndex(0)
    		->setCellValue('A1', 'title')
    		->setCellValue('B1', 'image_name');
    		$fila = 2;
    		foreach ($posts as $post) {
    			$objPHPExcel->setActiveSheetIndex(0)
    			->setCellValue("A$fila", $post->getTitle())
    			->setCellValue("B$fila", $post->getPathImage());
    			 
    			$fila++;
    		}
    		$objPHPExcel->setActiveSheetIndex(0);
    		$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    		$objWriter->save($rutaPostXlsx);
    		$zip->addFile( $rutaPostXlsx, pathinfo( "posts.xls", PATHINFO_BASENAME ) );
    		
    		
    		//WE GENERATE THE CSV FILE
    		$postCsv = $currentTime.".csv";
    		$ruta_temp = __DIR__.'/../../../../../web/temp/';
    		$rutaPostCsv = $ruta_temp.$postCsv;
    		
    		$response = $this->renderView('JIFrontendBundle:Default:postsCsv.html.twig', array('posts' => $posts));
    		file_put_contents($rutaPostCsv, $response);
    		$zip->addFile( $rutaPostCsv, pathinfo( "posts.csv", PATHINFO_BASENAME ) );
    		 
    		$zip->close();
    		
    		header('Content-Description: File Transfer');
    		header('Content-Transfer-Encoding: binary');
    		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    		header("Content-type: application/zip");
    		header("Content-Disposition: attachment; filename=posts");
    		header("Pragma: no-cache");
    		header("Expires: 0");
    		header('Content-Length: ' . filesize($zipname));
    		
    		readfile($zipname);
    		
    		//WE DELETE THE GENERATED FILES
    		unlink($rutaPostXlsx);
    		unlink($rutaPostCsv);
    		unlink($zipname);
    	}
    	catch (\Exception $e)
    	{
    		throw $e;//throw the exception and redirect to the custom error page located in app/Resources/TwigBundle/views/Exception/error.html.twig
    	}
    }
}
