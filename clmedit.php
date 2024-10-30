<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
    global $wpdb;
	$table=$wpdb->prefix."clm";
	$id_clm= intval($_GET["idedit"]) ;




	$clms=$wpdb->get_results("SELECT * FROM $table WHERE id_clm=$id_clm") ;
	$clms=$clms[0];


		$querystate= "WHERE link='{$clms->link}' AND link_title='{$clms->link_title}' ";






		$cuntres=$wpdb->get_results("SELECT * FROM $table ".$querystate);
		//

		     $countpost=count($cuntres);
		     $perajax = ceil( $countpost/1);
		     $percent = number_format( 1/$perajax,4) ;


		//

		$actual_link = (isset($_SERVER['HTTPS'])) ? "https" : "http";
		$actual_link.="://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$actual_link_li= $actual_link;
		$actual_link=explode('&', $actual_link);
		if(count( $actual_link)==3)
		{
		    unset($actual_link[count($actual_link)-1]);
		    $actual_link=implode('&',$actual_link);
		}else
		     {
		       $actual_link=implode('&', $actual_link);
		    }

             ?>

              <div class="wrap">







			<table class=" wp-list-table widefat fixed posts">

			<thead>
				<tr>

					<th id="title" class="manage-column column-tags"><?php _e("Row","clm") ?></th>
					<th id="author" class="manage-column column-tags"><?php _e("Link Content","clm") ?> </th>
					<th id="author" class="manage-column column-tags"><?php _e("Link","clm") ?></th>
					<th id="author" class="manage-column column-tags"><?php _e("Post Title","clm") ?></th>
					<th id="author" class="manage-column column-tags"><?php _e("Post Type","clm") ?></th>
					<th id="author" class="manage-column column-tags"><?php _e("Post ID","clm") ?></th>
					<th id="author" class="manage-column column-tags"><?php _e("Demo in Post ","clm") ?> </th>
				</tr>
			</thead>
			<tfoot>
				<tr>
          <th id="title" class="manage-column column-tags"><?php _e("Row","clm") ?></th>
          <th id="author" class="manage-column column-tags"><?php _e("Link Title","clm") ?> </th>
          <th id="author" class="manage-column column-tags"><?php _e("Link","clm") ?></th>
          <th id="author" class="manage-column column-tags"><?php _e("Post Title","clm") ?></th>
          <th id="author" class="manage-column column-tags"><?php _e("Post Type","clm") ?></th>
          <th id="author" class="manage-column column-tags"><?php _e("Post ID","clm") ?></th>
          <th id="author" class="manage-column column-tags"><?php _e("Demo in Post ","clm") ?> </th>
				</tr>
			</tfoot>
			<tbody id="the-list">
			    <?php
			    $ir= 1 ;
			    foreach ($cuntres as  $value) {?>

					<tr <?php if($value->id_clm==$id_clm) {echo ' style="background-color:#90CAF9"';} ?> >

						<td class="author column-author"><?php echo $ir ; ?></td>
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
						<td class="author column-author">

							<a target="_blank" href="<?php echo $value->post_url ?>">
							<?php
							 _e($value->post_title);
							 ?>
							 </a>

						</td>
						<td class="author column-author">
							<?php
							 _e($value->post_type);
							 ?>

							</td>
						<td class="author column-author"><?php echo $value->post_id ?></td>
						<td class="author column-author"><?php echo $value->atag ?></td>



					</tr>
				  <?php $ir++; }?>
				</tbody>
			   </table>

		   </div>



 <table class="form-table">
		 <form method="POST">

		  <tr class="top">
				<th class="titledesc" scope="row"><?php _e("Post Title","clm") ?>  </th>
				<td>
                 <?php echo $clms->post_title ?>
				</td>
		</tr>
		  <tr class="top">
				<th class="titledesc" scope="row"><?php _e("Post Type","clm") ?>  </th>
				<td>
                 <?php echo $clms->post_type ?>
				</td>
		</tr>
		  <tr class="top">
				<th class="titledesc" scope="row"> <?php _e("Post link","clm") ?>  </th>
				<td>
                 <?php echo $clms->post_url ?>
				</td>
		</tr>

		 <tr class="top">
				<th class="titledesc" scope="row"><?php _e("Content","clm") ?> </th>
				<td>
					<?php
			      wp_editor( $clms->atag,"atag" , ["media_buttons"=>false]);
			   ?>



				</td>
		</tr>



		<tr class="top">
				<th class="titledesc" scope="row">	Action  </th>
				<td>
                 <input checked  type="radio" name="action" value="siglepost"><?php _e("Edit just in this post ","clm") ?>
                 <br/><input   type="radio" name="action" value="allpost"><?php _e("Edit in all post","clm") ?>
				</td>
		</tr>


			<tr class="top">
				<th class="titledesc" scope="row"><?php _e("Edit Link","clm") ?>  </th>
				<td>
					<button data-nonce='<?php  echo wp_create_nonce( "clmpost");?>' id="editclm" data-id="<?php echo $id_clm?>"  type="button"   class="button button-primary"><?php _e("Send","clm") ?>   </button>
				</td>
			 </tr>
		 </form>
		</table>

     <div class="wrap clmadmin">

		<!-- JJ -->
		<div class="w3-light-grey none" id="pprocess">

		<div id="pageprocess"
		style="height:24px;width:1%"
		class="page"  data-ajax="<?php echo  $perajax ?>"
		data-step="<?php echo  $percent ?>"

s		>

		</div>

	</div>
		<!-- SDG -->
		 <span id="counter" >0</span> <?php _e("Out of ","clm") ?>  <?php echo  $countpost?> <?php _e("Post","clm") ?>

		 </div>
<?php
