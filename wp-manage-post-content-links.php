<?php
namespace ManagePostContentLinks;

/**
* Plugin Name: Manage post content links-interlinks
* Plugin URI:
* Description: A plugin for management link in post content like posts , pages , products ... or any post type
* Version: 1.1.0
* Author: Behzad rohizadeh
* Author URI:
*/



if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'CLM_ManagePostContentLinks' ) ) :


	class CLM_ManagePostContentLinks
	{

		function __construct()
		{
		 add_action('admin_menu',array(&$this, 'clm_add_menue'));
	      add_action( 'add_meta_boxes', array(&$this,'clm_add_meta_box'));
         add_action('admin_enqueue_scripts', array(&$this,'clm_add_js_css'));
         add_action('wp_ajax_clm_back_link',array(&$this,'wp_ajax_clm_back_link'));
         add_action( 'save_post', array(&$this,'clm_save_post_function'), 10, 3 );
         add_action('wp_ajax_clm_search_check',array(&$this,'wp_ajax_clm_search_check'));
         add_action('wp_ajax_clm_edit',array(&$this,'wp_ajax_clm_edit'));
	     register_activation_hook( __FILE__,array(&$this, 'clm_activate_pliugin_789' ));
         add_action('plugins_loaded',array(&$this, 'clm_localization_init_textdomain'));



		}

		function clm_add_menue()
		{

          add_menu_page(
          	      'clm',
          	       __("content links" , "clm"),
          	       'administrator',
          	       'clm_menue',
          	       array(&$this,'clm_menue_content'),
          	       " dashicons-editor-unlink"
          	     );
		     add_submenu_page(
		           'clm_menue',
		           "Setting",
		          __("Setting","clm"),
		          'administrator',
		          'clm_menue',
		         array(&$this,'clm_menue_content')

		        );

          	add_submenu_page(
          		   'clm_menue',
          		   "All",
          		   __("All Links", "clm"),
          		   'administrator',
          		   'clm_lists_link',
          		   array(&$this,'clm_lists_link')
          		);



		}

		function clm_lists_link()
		{


	  if (isset($_GET["idedit"])) {
	  	include("clmedit.php");
	  }

	   if (!isset($_GET["idedit"]))
	   {
	   include("clms.php");
	  }





		}


		function clm_menue_content()
		{

			 global $wpdb;
             	$qury = "";

			 $clm_post_type = get_option("clm_post_type") ;
			 if (!empty($clm_post_type)) {
			 	
             	foreach (@$clm_post_type as $key => $value) {
             		if ($key==0) {
             				$qury.="WHERE post_type ='{$value} ' ";
             		}
             		if ($key > 0) {
             				$qury.=" OR post_type ='{$value} ' ";
             		}
             	}
             }





	      	 $countpost=$wpdb->get_results("SELECT  COUNT(ID) from {$wpdb->prefix}posts ". $qury) ;
		     $countpost=intval(get_object_vars($countpost[0])["COUNT(ID)"]);
		     $perajax = ceil( $countpost/10);
		     $percent = number_format( 1/$perajax,4) ;

		     if (isset($_POST["post_type"])) {


		     	 if (  ! wp_verify_nonce( $_POST['clmpost'], 'clm' ) ) {
                    echo ('<div id="message" class="notice notice-error"><p>'.__("Nonce error","clm").'</p></div>');
                 }

		     	 if (   wp_verify_nonce( $_POST['clmpost'], 'clm' ) ) {
		      	$post_type=$this->sanitize_array( $_POST["post_type"]);
		      	update_option("clm_post_type",$post_type) ;
		      }

		      }

		     $post_type = get_post_types();
		     $clm_post_type =get_option("clm_post_type");

             
             if (!$clm_post_type) {
             	$clm_post_type = [] ; 
             }
    

			 ?>
  <div class="wrap clmadmin">

	<table class="form-table">
		 <form method="POST">

		 	<tr>
		 	
				<th class="titledesc" scope="row"><?php _e("How to used of the plugin","clm") ?>  </th>
		 		<td>
		 			<a target="_blank" href="https://www.youtube.com/watch?v=z1yAxc7yVq8"> Youtube</a>
		 		</td>

		 		
		 	</tr>

		 <tr class="top">
				<th class="titledesc" scope="row"><?php _e("Post type","clm") ?>  </th>
				<td>
					<select name="post_type[]"  multiple class="multiple">
					    <?php

					    foreach ($post_type as $key => $value) {

					    	$selected= "" ;

					    	if (in_array($value, $clm_post_type))
					    	{
					    		 $selected= "selected" ;
					    	}
					    	 ?>
                             <option <?php echo $selected ?> value="<?php echo $value ?>"><?php echo $value ?></option>

					    	 <?php

					            }

					            ?>
					</select>
				</td>
			 </tr>


			<tr class="top">
				<th class="titledesc" scope="row"> <?php _e("Setting","clm") ?>     </th>
				<td>
					<?php 
                       wp_nonce_field( "clm",  "clmpost",  false ,  true);
					?>
					<button  type="submit"  class="button button-primary"> <?php _e("Save Setting","clm") ?>    </button>
				</td>
			 </tr>
		 </form>
		</table>



  <table class="form-table">


			<tr class="top">
				<th class="titledesc" scope="row"> <?php _e("If you have just installed the plugin you can start now","clm") ?></th>
				<td>
					<button data-nonce='<?php  echo wp_create_nonce( "clmpost");?>' id="startsearch" type="button"   class="button button-primary"> <?php _e("Start","clm") ?>  </button>
				</td>
			 </tr>

		</table>

		<!-- JJ -->
		<div class="w3-light-grey none" id="pprocess">

		<div id="pageprocess"
		style="height:24px;width:1%"
		class="page" data-ajax="<?php echo  $perajax ?>" data-step="<?php echo  $percent ?>">

		</div>

	</div>
		<!-- SDG -->
		 <span id="counter" >0</span> <?php _e("out of","clm") ?>  <?php echo  $countpost?> <?php _e("Post","clm") ?>

</div>
			<?php
		}


		function clm_save_post_function($post_id, $post)
		{

			if (isset($_POST["action"]) && sanitize_text_field($_POST["action"])=="editpost")
			{
				$this->save_in_clm($post) ;
			}

		}

		function wp_ajax_clm_edit()
		{

			 global $wpdb;
	          $table=$wpdb->prefix."clm";
	          $id_clm= intval($_POST["id_clm"]) ;
	           $action =  sanitize_text_field($_POST["actionform"]);
               $ajaxcounter=intval($_POST['ajaxcounter']);
               $perajax=intval($_POST['perajax']);

	           $page=$ajaxcounter-1;
		       $offset =intval($page*1);

	           $clm=$wpdb->get_results("SELECT * FROM $table WHERE id_clm=$id_clm") ;
	           $clm=$clm[0];
               $clm_post_type = get_option("clm_post_type",true) ;
               $new_atag=htmlspecialchars_decode(stripslashes($_POST['atag']));

    try {




	        $atags = $this->getTextBetweenTags($new_atag,"a") ;
	      if (!empty($atags)) {

	      	 foreach ($atags as $key => $value) {



	      	 $a =@ new \SimpleXMLElement($value);
             $new_link = preg_replace('{/$}', '', $a['href']);



            }




        $new_ataghtml =$this->everything_in_tags($new_atag,"a");
	    $new_atagetext = strip_tags($new_ataghtml) ;



	    $befor_atag =  $clm->atag;
		$befor_link =  $clm->link;
        $befor_ataghtml =$clm->atag;
	    $befor_atagetext = $clm->link_title ;


	    $datain["link"] = $new_link;
		$datain["link_title"] =$new_atagetext;
        $datain["atag"] =$new_atag;
	    $datain["ataghtml"] =$new_ataghtml;

	    $args = [];
	    $clms_ups= [];
      if ($action=="siglepost") {
	   $clms_ups=$wpdb->get_results("SELECT * FROM $table WHERE id_clm=$clm->id_clm ") ;


        }

        if ($action=="allpost" && ($perajax!=$ajaxcounter ) ) {
	      $clms_ups=$wpdb->get_results("SELECT * FROM $table WHERE link='{$clm->link}' AND link_title='{$clm->link_title}' AND id_clm!=$id_clm LIMIT $offset,1 ") ;
        }

        if ($action=="allpost" && ($perajax==$ajaxcounter ) ) {
	      $clms_ups=$wpdb->get_results("SELECT * FROM $table WHERE id_clm==$id_clm ") ;
        }


foreach ($clms_ups as $key => $clmsingle) {



	      $args=[
	         	"post__in"=>[intval($clmsingle->post_id)] ,
	         	"post_type"=>$clm_post_type
	     ];


	        $posts = get_posts($args);
	        $res["postcount"] = count($posts) ;



	        foreach ($posts as $key => $post) {

	       $new_post_content = str_replace($clmsingle->atag, $new_atag, $post->post_content) ;




	        $dataup = array(
		        'ID' => $post->ID,
	            'post_content' =>$new_post_content,
	        );
          $result =wp_update_post($dataup, true);

          if ( is_wp_error( $result ) ) {
               }else {
	              $wpdb->update($table,$datain,["id_clm"=>$clmsingle->id_clm]);
                 }











		}

	}

  }
 } catch (\Exception $e) {

    }




           $res["status"]=200 ;
             echo json_encode($res);
             exit();

		}


		function save_in_clm( $post)
		{

			 global $wpdb;
             $table=$wpdb->prefix."clm";
             $post_id = $post->ID;
			$post_url = $post->guid;
			$post_title = $post->post_title;
	        $post_content= $post->post_content;
	        $post_type  =$post->post_type ;

	        try {





	        $atags = $this->getTextBetweenTags($post_content,"a") ;
	        if (!empty($atags)) {



            $wpdb->delete($table,array("post_id"=>$post_id));

	      foreach ($atags as $key => $value) {

        if (substr($value, 0, 2) == '<a' && substr($value, strlen($value) - 2, 2) =='a>' ) {

        	//echo $value ; exit() ;

	      $a= @ new \SimpleXMLElement($value) ;



             $link = preg_replace('{/$}', '', $a['href']);

	      	$clms=$wpdb->get_results("SELECT * FROM $table WHERE post_id=$post_id AND link='$link'") ;


	         if (count($clms) == 0) {
           //echo $a['href'];
	      	$ataghtml =$this->everything_in_tags($value,"a");
	      	$atagetext = trim( strip_tags($ataghtml) );

	      	    $datain["link"] = $link;
				$datain["link_title"] =$atagetext;
				$datain["post_id"] =$post_id;
				$datain["post_url"] =$post_url;
				$datain["post_title"] =$post_title;
           		$datain["atag"] =$value;
				$datain["ataghtml"] =$ataghtml;
				$datain["post_type"] = $post_type ;
	            $in=$wpdb->insert($table,$datain);

	      }
	  }
	}
   }
}catch (\Exception $e) {
	        	 return true ;
	        }

    return true ;


		}

		function everything_in_tags($string, $tagname)
		{
		    $pattern = "#<\s*?$tagname\b[^>]*>(.*?)</$tagname\b[^>]*>#s";
		    preg_match($pattern, $string, $matches);
		    return $matches[1];
		}


		function getTextBetweenTags($string, $tagname) {
		  /* $pattern = "/<$tagname ?.*>(.*)<\/$tagname>/";
		    preg_match_all($pattern, $string, $matches);
		    return $matches[0];*/
		    preg_match_all('/<a[^>]*>(.*?)<\/a>/i', $string, $matches);

		    return $matches[0];

		}

		function wp_ajax_clm_search_check()
		{

             if (isset($_POST["ajaxcounter"])) {

             	$ajaxcounter= intval($_POST["ajaxcounter"]) ;
             	$clm_post_type = get_option("clm_post_type",true) ;

             	$listingPages = get_posts(
			    array(
				    'posts_per_page' => 10,
				    'post_type'=>$clm_post_type,
				    'paged' =>$ajaxcounter,
			    )
			);


             foreach ($listingPages as $key => $post) {

             	 $this->save_in_clm($post);

             }


             }



			 $res["status"]=200 ;
             echo json_encode($res);
             exit();
		}


function sanitize_array( $array ) {
   foreach ( (array) $array as $k => $v ) {

          $array[$k] = sanitize_text_field( $v );
      }
         
  return $array;                                                       
}

		function wp_ajax_clm_back_link()
		{

			$data = $this->sanitize_array($_POST["data"]) ;
			$post_id = intval($_POST["post_id"]) ;
			$post_url = esc_url_raw($_POST["post_url"] );
			$post_title =sanitize_text_field( $_POST["post_title"]) ;


			 global $wpdb;
             $table=$wpdb->prefix."clm";

	         foreach ($data as $key => $value) {

	         $link = $value[1];
	         $title= $value[0];
	         $clms=$wpdb->get_results("SELECT * FROM $table WHERE post_id=$post_id AND link='$link'") ;

	         if (count($clms) == 0) {
				$datain["link"] =$link;
				$datain["link_title"] =$title;
				$datain["post_id"] =$post_id;
				$datain["post_url"] =$post_url;
				$datain["post_title"] =$post_title;

	           // $in=$wpdb->insert($table,$datain);
	         }



	         }

			 $res["status"]=200 ;
             echo json_encode($res);
             exit();
		}

		function clm_add_js_css()
		{

		wp_enqueue_script('clm', plugin_dir_url(__FILE__) . 'js/clm.js', array('jquery'),"1.0.");

		wp_register_style('clm_css', plugins_url('/css/clm.css', __FILE__) ,null,"1.0.2" );
        wp_enqueue_style( 'clm_css' );

		wp_localize_script( 'clm', 'the_in_url', array( 'in_url' => admin_url( 'admin-ajax.php' ) ) );


		}

	   function	clm_add_meta_box()
		{

			add_meta_box( "clm",__("Content link","clm"), array(&$this,'clm_meta_box_content'), array('product',"post","page"),'advanced', 'default');

		}

		function clm_meta_box_content($post)


		{

			$post_url = $post->guid;
			$post_id = $post->ID;
			$post_content = $post->post_content;
			$post_title = $post->post_title;

			 global $wpdb;
             $table=$wpdb->prefix."clm";
	         $clms=$wpdb->get_results("SELECT * FROM $table WHERE post_id=$post_id") ;  ?>


          <div  data-id=".$post_id."   data-url=".$post_url."  data-title=".$post_title." >


			<table class=" wp-list-table widefat fixed posts">

            <thead>
				<tr>

					<th id="title" class="manage-column column-tags"><?php _e("Row","clm") ?></th>
					<th id="author" class="manage-column column-tags"><?php _e("Link Title","clm") ?>  </th>
					<th id="author" class="manage-column column-tags"><?php _e("Link","clm") ?></th>
					<th id="author" class="manage-column column-tags"><?php _e("Demo in Post","clm") ?>  </th>
				</tr>
			</thead>
			<?php
          	   foreach ($clms as $key => $value) {

	         $counter=$wpdb->get_results("SELECT * FROM $table WHERE link='$value->link' ") ;
	         ?>


          		 <tr  >

						<td class="author column-author"><?php echo ($key+1) ; ?></td>
						<td class="post-title page-title column-title">
							<strong>
								<a class="row-title" href="<?php echo admin_url('admin.php?page=clm_lists_link&idedit='.$value->id_clm);?>" title="">
								<?php
										echo $value->link_title;
								 ?>

								</a>
							</strong>

						</td>
						<td class="author column-author">
							<?php
							 _e($value->link);
							 ?>

							</td>


						<td class="author column-author"><?php echo $value->atag ?></td>



					</tr>

          <?php 	}  ?>


          	</tbody>

          </table>

           </div>



       <?php

		}


		function clm_localization_init_textdomain()
		{

			$path = dirname(plugin_basename( __FILE__ )) . '/lang/';
	        $loaded = load_plugin_textdomain( 'clm', false, $path);
		}


		function clm_activate_pliugin_789()
		{

        global $wpdb;
		$table=$wpdb->prefix."clm";
		if($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table)
		{

			$sql="CREATE TABLE IF NOT EXISTS `$table` (
			  `id_clm` int(255) NOT NULL AUTO_INCREMENT,
			  `link_title` varchar(255) NOT NULL,
			  `link` varchar(255) NOT NULL,
			  `post_url` varchar(255) NOT NULL,
			  `post_title` varchar(255) NOT NULL,
			  `post_id` int(255) NOT NULL,
			  `atag` varchar(255) DEFAULT NULL,
			  `ataghtml` varchar(255) DEFAULT NULL,
			  `post_type` varchar(255) DEFAULT NULL ,
			   PRIMARY KEY (`id_clm`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;";




		  $wpdb->query($sql);
		}






      }

	}
	new CLM_ManagePostContentLinks() ;


 endif;
