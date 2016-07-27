<?php

namespace ByTIC\Common\Controllers\Traits\Async;

use ImgPicker;

trait Gallery
{
    public function uploadGallery()
    {
        $item = $this->checkItem();

        $options = array(

            // Upload directory path
            'upload_dir' => TMP_PATH,

            // Upload directory url:
            'upload_url' => TMP_URL,

            // Accepted file types:
            'accept_file_types' => 'png|jpg|jpeg|gif',

            // Directory mode:
            'mkdir_mode' => 0777,

            // File size restrictions (in bytes):
            'max_file_size' => null,
            'min_file_size' => 1,

            // Image resolution restrictions (in px):
            'max_width' => null,
            'max_height' => null,
            'min_width' => $item->getImageWidth(),
            'min_height' => $item->getImageHeight(),

            /**
             *    Load callback
             *
             * @param    ImgPicker $instance
             * @return string|array
             */
            'load' => function ($instance) {
                //return 'avatar.jpg';
            },

            /**
             *    Delete callback
             *
             * @param  string $filename
             * @param    ImgPicker $instance
             * @return boolean
             */
            'delete' => function ($filename, $instance) {
                return true;
            },

            /**
             *    Upload start callback
             *
             * @param    stdClass $image
             * @param    ImgPicker $instance
             * @return void
             */
            'upload_start' => function ($image, $instance) {
                $image->name = '~toCropFP' . $item->ID . '-' . rand(1000, 4000) . '.' . $image->type;
            },

            /**
             *    Upload complete callback
             *
             * @param    stdClass $image
             * @param    ImgPicker $instance
             * @return void
             */
            'upload_complete' => function ($image, $instance) {
            },

            /**
             *    Crop start callback
             *
             * @param    stdClass $image
             * @param    ImgPicker $instance
             * @return void
             */
            'crop_start' => function ($image, $instance) {
                $image->name = sha1(microtime()) . '.' . $image->type;
            },

            /**
             *    Crop complete callback
             *
             * @param    stdClass $image
             * @param    ImgPicker $instance
             * @return void
             */
            'crop_complete' => function ($image, $instance) {
                foreach (array('full', 'default') as $version) {
                    $filename = $instance->getVersionFilename($image->name, $version);
                    $filepath = $instance->getUploadPath($filename, $version);
                    rename($filepath, str_replace('-' . $version . '.', '.', $filepath));
                }
            }
        );

        if (@$_POST['action'] == 'crop') {
            // Image versions:
            $options['versions'] = array(
                'full' => array(
                    'upload_dir' => $item->getImageBasePath('full'),
                    'upload_url' => $item->getImageBaseURL('full'),
                    'max_width' => 1600,
                    'max_height' => 1600
                ),
                'default' => array(
                    'upload_dir' => $item->getImageBasePath('default'),
                    'upload_url' => $item->getImageBaseURL('default'),
                    'max_width' => $item->getImageWidth(),
                    'crop' => $item->getImageHeight()
                ),
            );
        }

        // Create new ImgPicker instance
        new ImgPicker($options);
        die('');
    }
}