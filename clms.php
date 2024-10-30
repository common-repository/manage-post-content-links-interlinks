<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

   global $wpdb;
	$table=$wpdb->prefix."clm";
    
        $page=1;
		if(isset($_GET['pag']))
		 {
		  $page=intval($_GET['pag']);
		}

		$querystate= "";





		if ( isset($_GET["p"])) {
			$s=sanitize_text_field($_GET["p"]);

			if (empty($querystate)) {

				$querystate.=" WHERE link_title LIKE '%{$s}%' OR link LIKE '%{$s}%' OR post_title LIKE '%{$s}%' ";
			}
		}
		//$querystate ="WHERE link='https://pcprinter.ir/product-category/consuming-materials-printer-and-photopcopier/cartridge-printer-photocopier' ";
		$page=$page-1;
		$offset =intval($page*50);
		$cuntres=$wpdb->get_results("SELECT {$table}.*, COUNT(*) as num FROM $table ".$querystate."  group by link,link_title  ORDER BY num DESC LIMIT $offset,50 ");
		$cuntr=$wpdb->get_results("SELECT COUNT(id_clm) AS paged FROM $table group by link,link_title");
		$pagenations=count($cuntr);
		//


		//
		$page=$page+1;
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
			$actual_link_li=remove_query_arg( 'userin', $actual_link_li );
			$actual_link_li=remove_query_arg( 'export', $actual_link_li );

             ?>

              <div class="wrap">
            <div class="tablenav-pages" style="float:left;">
            <form  method="GET">
				Paganation: <?php  echo ceil($pagenations/50);?>
				<a class="next-page" href="<?php echo add_query_arg( 'pag', $page+1, $actual_link_li ); ?>"><span class="screen-reader-text">Next </span><span aria-hidden="true"><</span></a>
				<?php
				$al=explode('&',$actual_link);
				$pt=explode('?',$al[0]);
				$pa=explode('=',$pt[1]);
				echo ('<input type="hidden" value="'.$pa[1].'" name="'.$pa[0].'">');
				unset($al[0]);
				foreach ($al as $val){
					$rt=explode('=',$val);
				?>
				<input type="hidden" value="<?php echo $rt[1]?>" name="<?php echo $rt[0]?>">
				<?php }


		        ?>
				<input style="width: 40px;" type="text" id="current-page-selector" class="current-page" name="pag" value="<?php echo $page;?>" />
				<a class="last-page" href="<?php echo add_query_arg( 'pag', $page-1, $actual_link_li ); ?>"><span class="screen-reader-text">Peri</span><span aria-hidden="true">></span></a>
			 </form>
			</div>
			  <form action="" method="GET" style="float: left">
			  	<div class="alignleft actions bulkactions" >
			  	<input type="hidden" value="clm_lists_link" name="page" size="15">
			  	<input type="text" placeholder="<?php _e("Search ... ","clm") ?>" name="p" size="15" >
			  	<input  id="doaction" class="button action" value="<?php _e("Search","clm") ?>" type="submit">
			  </div>
			  </form>






			<table class=" wp-list-table widefat fixed posts">

			<thead>
				<tr>
          <th id="title" class="manage-column column-tags"><?php _e("Row","clm") ?></th>
          <th id="author" class="manage-column column-tags"><?php _e("Link Content","clm") ?> </th>
          <th id="author" class="manage-column column-tags"><?php _e("Link","clm") ?></th>
          <th id="author" class="manage-column column-tags"><?php _e("Post Title","clm") ?></th>
					<th id="author" class="manage-column column-tags"><?php _e("Post Type","clm") ?></th>
	        <th id="author" class="manage-column column-tags"> <?php _e("Count this link in all post type","clm") ?>  </th>
				</tr>
			</thead>
			<tfoot>
        <tr>
          <th id="title" class="manage-column column-tags"><?php _e("Row","clm") ?></th>
          <th id="author" class="manage-column column-tags"><?php _e("Link Content","clm") ?> </th>
          <th id="author" class="manage-column column-tags"><?php _e("Link","clm") ?></th>
          <th id="author" class="manage-column column-tags"><?php _e("Post Title","clm") ?></th>
          <th id="author" class="manage-column column-tags"><?php _e("Post Type","clm") ?></th>
          <th id="author" class="manage-column column-tags"> <?php _e("Count","clm") ?>  </th>
        </tr>
			</tfoot>
			<tbody id="the-list">
			    <?php
			    $ir= 1 ;
			    foreach ($cuntres as  $value) {?>
					<tr id="post-293" class="post-293 type-post status-draft format-standard hentry category-- alternate iedit author-self level-0">

						<td class="author column-author"><?php echo ($page-1)*50+$ir ; ?></td>
						<td class="post-title page-title column-title">
							<strong>
								<a class="row-title" href="<?php echo admin_url('admin.php?page=clm_lists_link&idedit='.$value->id_clm);?>" title="">
								<?php
										_e( $value->link_title);
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

						<td class="author column-author">
							<?php
							 _e($value->num);
							 ?>

						</td>

					</tr>
				  <?php $ir++; }?>
				</tbody>
			   </table>
			
		</div>
<?php
