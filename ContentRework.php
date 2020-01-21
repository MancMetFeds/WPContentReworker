<?php
require '../wp-load.php';
function getSimilarByTitle($post){
    // returns posts with mathing title
    //query needs to return 1 post
    //TODO
    return  $GLOBALS['wpdb']->get_results("SELECT ID FROM wp_posts WHERE post_title = '{$post->post_title}' and ID <> '{$post->ID}'", OBJECT);
    // return  $GLOBALS['wpdb']->get_results("SELECT object_id FROM wp_posts, wp_term_relationships WHERE wp_posts.post_title = '{$post->post_title}' and wp_term_relationships.term_taxonomy_id = 34 and object_id <> '{$post->ID}'", OBJECT);
}


//  query get all premium posts from db
$premiumPosts= $GLOBALS['wpdb']->get_results("SELECT object_id FROM wp_term_relationships WHERE term_taxonomy_id = 34", ARRAY_A);
$postCount = count($premiumPosts);

Print($postCount." premium posts found using query processing...\n");


//created pointer for csv
$file = fopen('php://output', 'w');                                                                                   //  creates pointer for csv file if non exists otherwise opens file.

//csv headers
fputcsv($file, array('freemium ID/s', 'Premium ID', 'old  premium permalink', 'new premium permalink'));
Print("\n\n");


$data=array();
$count=0;

foreach($premiumPosts as $post){
    $currentPost = get_post($post);
  //  try{
    //Print("\r\t".$count++."/".count($premiumPosts));
    $premiumID=$currentPost->ID;
    $freemiumID = getSimilarByTitle($currentPost);
    foreach ($freemiumID as $id){
        var_dump(get_post($id));
    }
    if (!empty($freemiumID)) {
        if (count($freemiumID) > 1){
            Print("Post with ID ".$freemiumID." skipped as more than expected posts with the same title exist");
            //var_dump(get_post($freemiumID));
            continue;
        }
        $postRecord= array($freemiumID, $premiumID, get_permalink($premiumID));
        $premiumPost = get_post($premiumID)->post;
        // get categories in param 1 that ARE NOT in param 2 see 'relative set complement' for further explanation.
        $categoryComplement=array_diff(wp_get_post_categories($freemiumID), wp_get_post_categories($premiumID));

        //wp_set_post_categories($post->ID, $categoryComplement, $append = true);                                   //  only uncomment when staged
        var_dump($freemiumID);
        die();
        //var_dump(wp_get_post_tags($freemiumID[0])->name);
        //$tagComplement=array_diff(, wp_get_post_tags($premiumID));
        //wp_set_post_tags($post->ID, $tagComplement, $append = true);                                              //  only uncomment when staged
        array_push($postRecord, get_permalink($premiumID));
        array_push($data, $postRecord);

        //wp_delete_post($freemiumID);                                                                              //  only uncomment when staged
    }
//    } catch(Exception $e) {           // commented out for testing
//        //do nothing
//    }

}

foreach ($data as $row)
{
    fputcsv($file, $row);
}
exit();
?>
