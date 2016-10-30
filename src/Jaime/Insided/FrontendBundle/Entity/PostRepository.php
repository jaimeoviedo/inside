<?php

namespace Jaime\Insided\FrontendBundle\Entity;

class PostRepository extends \Doctrine\ORM\EntityRepository
{
	
	public function findPostOrderedByFecha() {
	
		$query = "SELECT p FROM JIFrontendBundle:Post p 
						ORDER BY p.date DESC";
	
		$query = $this->getEntityManager()->createQuery($query);
		$posts = $query->getResult();
	
		return $posts;
	}
	
}
