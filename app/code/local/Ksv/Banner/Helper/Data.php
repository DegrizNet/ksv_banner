<?php
class Ksv_Banner_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
     * Path for config.
     */
    const XML_CONFIG_PATH = 'banner/general/';

    /**
     * Name library directory.
     */
    const NAME_DIR_JS = 'banner/jquery/';

    /**
     * List files for include.
     *
     * @var array
     */
    protected $_files = array(
        'jquery-1.8.1.min.js',
        'jquery.noconflict.js',
    );

    /**
     * Check enabled.
     *
     * @return bool
     */
    public function isJqueryEnabled()
    {
        return (bool) $this->_getConfigValue('jquery', $store = '');
    }

    /**
     * Return path file.
     *
     * @param $file
     *
     * @return string
     */
    public function getJQueryPath($file)
    {
        return self::NAME_DIR_JS . $file;
    }

    /**
     * Return list files.
     *
     * @return array
     */
    public function getFiles()
    {
        return $this->_files;
    }

	public function isBannerModuleEnabled()
    {
        return (bool) $this->_getConfigValue('active', $store = '');
    }
	
	public function isResponsiveBannerEnabled()
    {
        return (bool) $this->_getConfigValue('responsive_banner', $store = '');
    }
	
    protected function _getConfigValue($key, $store)
    {
        return Mage::getStoreConfig(self::XML_CONFIG_PATH . $key, $store = '');
    }
	
	public function resizeImg($fileName, $width, $height = '', $aspect = true)
	{
		$baseURL = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
		$imageURL = $baseURL . '/' . 'banner' . '/' . $fileName;

		$basePath = Mage::getBaseDir('media');
		$imagePath = $basePath . DS . 'banner' . str_replace('/', DS, $fileName);

		$extra = $width . 'x' . $height;
		$resizedImagePath = $basePath . DS . 'banner' . DS . 'resized' . DS . $extra . str_replace('/', DS, $fileName);

		// If both width and height are provided
		if ($width != '' && $height != '') {
			// If the image has already been resized or cropped, just return the URL
			if (file_exists($resizedImagePath) && is_file($resizedImagePath)) {
				$resizedURL = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . "banner" . '/' . "resized" . '/' . $extra . $fileName;
			} else {
				// Resize the image first
				$imageObj = new Varien_Image($imagePath);
				$imageObj->constrainOnly(true);
				$imageObj->keepAspectRatio(true);
				$imageObj->keepFrame(false);
				$imageObj->backgroundColor(array(255, 255, 255));

				// Calculate the resize dimensions based on aspect ratio
				$imageWidth = $imageObj->getOriginalWidth();
				$imageHeight = $imageObj->getOriginalHeight();

				$resizeWidth = $width;
				$resizeHeight = $height;

				if ($aspect) {
					$targetRatio = $width / $height;
					$currentRatio = $imageWidth / $imageHeight;

					if ($currentRatio > $targetRatio) {
						// Resize based on height
						$resizeHeight = $height;
						$resizeWidth = $height * ($imageWidth / $imageHeight);
					} else {
						// Resize based on width
						$resizeWidth = $width;
						$resizeHeight = $width * ($imageHeight / $imageWidth);
					}
				}

				$imageObj->resize($resizeWidth, $resizeHeight);
				$imageObj->save($resizedImagePath);

				// Crop the resized image to the exact size
				$imageObj = new Varien_Image($resizedImagePath);
				$imageObj->constrainOnly(false);
				$imageObj->backgroundColor(array(255, 255, 255));

				// Calculate the crop dimensions based on aspect ratio
				$imageWidth = $imageObj->getOriginalWidth();
				$imageHeight = $imageObj->getOriginalHeight();

				$cropWidth = $width;
				$cropHeight = $height;

				if ($aspect) {
					$imageObj->keepAspectRatio(false);
					$imageObj->keepFrame(false);

					$targetRatio = $width / $height;
					$currentRatio = $imageWidth / $imageHeight;

					if ($currentRatio > $targetRatio) {
						// Crop horizontally
						$cropWidth = $imageHeight * $targetRatio;
					} else {
						// Crop vertically
						$cropHeight = $imageWidth / $targetRatio;
					}

					// Calculate the crop position
					$diffWidth   = ($imageWidth - $cropWidth) / 2;
					$diffHeight  = ($imageHeight - $cropHeight) / 2;

					$imageObj->crop(
						floor($diffHeight),
						floor($diffWidth),
						ceil($diffWidth),
						ceil($diffHeight)
					);
				} else {
					$imageObj->keepAspectRatio(true);
					$imageObj->keepFrame(true);
					$imageObj->resize($width, $height);
				}

				$imageObj->save($resizedImagePath);

				$resizedURL = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . "banner" . '/' . "resized" . '/' . $extra . $fileName;
			}
		} else {
			$resizedURL = $imageURL;
		}

		return $resizedURL;
	}



	
}