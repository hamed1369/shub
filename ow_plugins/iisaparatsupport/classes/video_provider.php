<?php
/**
 * User: Hamed Tahmooresi
 * Date: 5/1/2016
 * Time: 10:16 AM
 */

class VideoProviderAparat
{

    public static function getThumbUrl( $code )
    {
        $url = null;
        if (preg_match_all(IISAPARATSUPPORT_BOL_Service::getInstance()->getAparatNewEmbedCodeRegex(), $code, $matches)){
            $uid = $matches[1][0];
            $content = @file_get_contents($uid);
            if(!preg_match_all('/(?:http|https):\/\/www\.aparat\.com\/video\/video\/embed\/videohash\/\w+\/vt\/frame/i', $content, $matches)){
                return VideoProviders::PROVIDER_UNDEFINED;
            }
            $url = $matches[0][0];
        }
        if (preg_match_all(IISAPARATSUPPORT_BOL_Service::getInstance()->getAparatOldEmbedCodeRegex(), $code, $matches)){
            $url = $matches[1][0];
        }
        if (!$url)
            return VideoProviders::PROVIDER_UNDEFINED;

        $content = @file_get_contents($url);
        if(!preg_match_all('/style="background-image\:\s+url\(\'((?:http|https):\/\/[\w\.\/-]+)\'\)/i',$content,$matches)){
           return VideoProviders::PROVIDER_UNDEFINED;
        }
        $url = $matches[1][0];
        $url = str_replace('https','http',$url);
        return !empty($url) ? $url : VideoProviders::PROVIDER_UNDEFINED;
   }
}