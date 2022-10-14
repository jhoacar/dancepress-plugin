<?php
namespace DancePressTRWA\Models;

class Image extends Model
{
    public function __construct($sessionCondition = '')
    {
        parent::__construct($sessionCondition);
    }

    /* This function attaches the image to the post in the database, add it to functions.php */
    /*From http://madebyraygun.com/blog/2012/upload-and-attach-multiple-images-from-the-wordpress-front-end/ */
 
    public function insert_attachment($id=false)
    {
        $errors = false;
        require_once(ABSPATH . "wp-admin" . '/includes/image.php');
        require_once(ABSPATH . "wp-admin" . '/includes/file.php');
        require_once(ABSPATH . "wp-admin" . '/includes/media.php');
        
        $upload_overrides = array('test_form' => false);
        
        if (!isset($_FILES['upload_attachment'])) {
            $errors['no image files found'];
            return;
        }
        
        $files = array();
        
        //sort $_FILES in a more useful way
        foreach ($_FILES['upload_attachment'] as $key => $value) {
            foreach ($value as $k => $v) {
                $files[$k][$key] = $v;
            }
        }
        
        if (count($files) > 3) {
            $errors[] = "You can upload a maximum of three images. Please try again with fewer images.";
        }
         
        $attachments = array();
        
        foreach ($files as $file) {
            if ($file['size'] > 102400) {
                $errors[] = "One of the images uploaded was too large";
                continue;
            }
            if ($file['error'] !== 0) {
                $errors[] = "A file upload error was encountered";
                continue;
            }
            if ($file['type'] != 'image/jpeg' && $file['type'] != 'image/png' && $file['type'] != 'image/gif') {
                $errors[] = "Uploads included disallowed file types";
            }
         
            $attachments[] = wp_handle_upload($file, $upload_overrides);
        
            //Don't bother saving image data in Wordpress tables - just store with user in DS tables.
        }
        
        if (!$attachments) {
            return;
        }
        
        $this->saveUserImageData($attachments, $id);
        
        return $errors;
    }
    
    
    private function saveUserImageData($imgs, $id = false)
    {
        $imgs = json_encode($imgs);
        
        if (!is_admin() && !$id) {
            $sql = $this->db->prepare(
                "
					UPDATE
						{$this->p}dance_jobs
					SET
						images = %s
					WHERE
						user_id = %d
				",
                $imgs,
                $this->userid
            );

            $this->db->query($sql);
        } else {//If admin, attach image by DL id, not user ID.
            
            $sql = $this->db->prepare(
                "
					UPDATE
						{$this->p}dance_jobs
					SET
						images = %s
					WHERE
						id = %d
				",
                $imgs,
                $id
            );

            $this->db->query($sql);
        }
        return $this->db->last_result;
    }
}
