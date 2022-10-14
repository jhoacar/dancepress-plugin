<?php include_once('parent-portal-menu.php'); ?>
<h3>Portal Home</h3>
<div id='dptrwa-portal-login'>
<?php
    if (!is_user_logged_in()) {
        wp_login_form(['form_id' => 'dbtrwa-loginform']);
    }
 ?>
</div>
<?php _e('Through the Parent Portal area you can keep up to date with the latest news and announcements from');?> <?=get_bloginfo('name');?>, <?php _e('view and edit your profile, view your student\'s classes, and view and print out your scheduled payment records.');?>

<h1><?php _e('News');?></h1>
<?php if (!$news) : ?>
	<?php if (current_user_can('administrator')):?>
		<div><?php _e('Administrator message: News items and posts that you create and assign to a special category called "Portal News" will appear here and not be accessible to non-registered users. Client\'s must have paid, or the DancePress admin must validate the client\'s registration manually for access to be available.');?></div>
	<?php endif; ?>
		<div><?php _e('News items and information will appear here when they are added by the site administrators.');?></div>
<?php else:?>

<ul id="pp-news">
<?php 	foreach ($news as $post):
        setup_postdata($post);
        $permalink = get_permalink($post->ID);
?>

<article id="post-818" class="post-818 post type-post status-publish format-standard hentry category-company-posts category-db-login-posts">
	<header class="entry-header">
		<h2 class="entry-title">
			<?=$post->post_title;?>
		</h2>
	</header>
	<!-- .entry-header -->
	<div class="entry-content"><?=$post->post_content;?></div>
	<!-- .entry-content -->
	<footer class="entry-meta">Published: <?=$post->post_date;?></footer>
	<!-- .entry-meta -->
</article>


<?php
    endforeach;
?>
</ul>

<?php endif; ?>
