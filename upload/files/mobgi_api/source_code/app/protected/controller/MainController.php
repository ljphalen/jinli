<?php
/**
 * MainController
 * Feel free to delete the methods and replace them with your own code.
 *
 * @author darkredz
 */
class MainController extends DooController{

    public function index(){
		//Just replace these
        if(Doo::conf()->APP_MODE!="prod"){
            Doo::loadCore('app/DooSiteMagic');
            DooSiteMagic::displayHome();
        }else{
            header("HTTP/1.1 404 Not Found");
        }
    }
//	
//	public function allurl(){	
//		Doo::loadCore('app/DooSiteMagic');
//		DooSiteMagic::showAllUrl();	
//	}
//	
//    public function debug(){
//		Doo::loadCore('app/DooSiteMagic');
//		DooSiteMagic::showDebug($this->params['filename']);
//    }
//	
//	public function gen_sitemap_controller(){
//		//This will replace the routes.conf.php file
//		Doo::loadCore('app/DooSiteMagic');
//		DooSiteMagic::buildSitemap(true);		
//		DooSiteMagic::buildSite();
//	}
//	
//	public function gen_sitemap(){
//		//This will write a new file,  routes2.conf.php file
//		Doo::loadCore('app/DooSiteMagic');
//		DooSiteMagic::buildSitemap();		
//	}
//	
//	public function gen_site(){
//		Doo::loadCore('app/DooSiteMagic');
//		DooSiteMagic::buildSite();
//	}
//	 
    public function gen_model(){
        if(Doo::conf()->APP_MODE!="prod"){
            Doo::loadCore('db/DooModelGen');
            #Doo::db()->reconnect("ads");
            DooModelGen::genMySQL(true,true,"AppModel",true,"Base",null,Doo::conf()->SITE_PATH . Doo::conf()->PROTECTED_FOLDER . 'model/datamodel/');
        }else{
            header("HTTP/1.1 404 Not Found");
        }
    }
    public function delviewc(){
        $filename=$_GET["filename"];
        if(empty($filename)){
            die("please input filename");
        }
        $filename=  explode(",",$filename);
        if(is_array($filename)){
            Doo::loadPlugin("function");
            foreach($filename as $file){
                $filepath=Doo::conf()->SITE_PATH."protected/viewc/".Doo::conf()->TEMPLATE_PATH."/".$file;
                if(file_exists($filepath)){
                    if(is_dir($filepath)){
                        remove_directory($filepath);
                    }else{
                        echo "removing ".$filepath."<br>";
                        @unlink($filepath);
                    }
                }else{
                    echo $filepath." is not exists<br>";
                }
            }
        }
    }
}
?>