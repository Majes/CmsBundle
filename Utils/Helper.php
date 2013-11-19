<?php
namespace Majes\CmsBundle\Utils;

class Helper
{

	static function extractBreadcrumb($menu, $breadcrumb = array()){

		foreach($menu as $page){

			if($page['current']){

				$page_current = $page;
				unset($page_current['children']);
				$breadcrumb[] = $page_current;

				if(!is_null($page['children'])){
					$breadcrumb = self::extractBreadcrumbRecursive($page['children'], $breadcrumb);
					break;
				}

			}

		}
		return $breadcrumb;
		
	}

	static function extractBreadcrumbRecursive($menu, $breadcrumb){

		foreach($menu as $page){

			if($page['current']){

				$page_current = $page;
				unset($page_current['children']);
				$breadcrumb[] = $page_current;

				if(!is_null($page['children'])){
					self::extractBreadcrumbRecursive($page['children'], $breadcrumb);
					break;
				}

			}

		}

		return $breadcrumb;

	}



	static function rrmdir($dir) {
        foreach(glob($dir . '/*') as $file) {
            if(is_dir($file))
                self::rrmdir($file);
            else
                unlink($file);
        }
        rmdir($dir);
    }


    static function hasAdminRole($page, $securityContext){

    	$roles = array('ROLE_SUPERADMIN'); $page_roles = array();
        if(!is_null($page)){
            $page_roles = $page->getRoles();    
            foreach($page_roles as $page_role){
                $roles[] = $page_role->getRole();
            }
        }

        if( !$securityContext->isGranted($roles) && count($page_roles) > 0)
            return false;

        return true;

    }

}