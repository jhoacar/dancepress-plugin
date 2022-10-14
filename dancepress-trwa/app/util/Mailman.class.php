<?php
namespace DancePressTRWA\Util;

//DancePressTRWA
use DancePressTRWA\Models\Option;

final class Mailman
{
    public function send(array $options)
    {
        $option = new Option();
        
        $contactEmail = $option->getContactEmail();
        $blogName = get_bloginfo('name');
        
        $headers = array(
            "From: {$blogName} <{$contactEmail}>",
            "Content-type:text/html"
        );
        
        $to = $options['to'];
        $subject = $options['subject'];
        $subject = "[$blogName] " . $subject;
        $additionalHeaders = isset($options['headers']) ? $options['headers'] : array();
        $attachments = isset($options['attachments']) ? $options['attachments'] : array();
        $templateParameters = isset($options['template_parameters']) ? $options['template_parameters'] : array();
        $templatePath = PLUGIN_DOCUMENT_ROOT . '/email_templates/' . $options['template'] . '.php';
        $templatePremiumPath = PLUGIN_DOCUMENT_ROOT . '/email_templates/' . $options['template'] . '__premium_only.php';
        
        if ($additionalHeaders) {
            $headers = array_merge($headers, $additionalHeaders);
        }

        extract($templateParameters);
        
        ob_start();
        
        if (file_exists($templatePath)) {
            include($templatePath);
        } elseif (file_exists($templatePremiumPath) && dancepress_fs()->is_plan__premium_only('pro')) {
            include($templatePremiumPath);
        } else {
            ob_end_clean();
            return false;
        }
        
        $message = ob_get_clean();
        
        return wp_mail($to, $subject, $message, $headers, $attachments);
    }
}
