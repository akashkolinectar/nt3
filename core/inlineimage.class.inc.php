<?php


define('INLINEIMAGE_DOWNLOAD_URL', 'pages/ajax.document.php?operation=download_inlineimage&id=');

class InlineImage extends DBObject
{
	public static function Init()
	{
		$aParams = array
		(
			'category' => 'addon',
			'key_type' => 'autoincrement',
			'name_attcode' => array('item_class', 'temp_id'),
			'state_attcode' => '',
			'reconc_keys' => array(''),
			'db_table' => 'inline_image',
			'db_key_field' => 'id',
			'db_finalclass_field' => '',
			'indexes' => array(
				array('temp_id'),
				array('item_class', 'item_id'),
				array('item_org_id'),
			),
		);
		MetaModel::Init_Params($aParams);
		MetaModel::Init_InheritAttributes();
		MetaModel::Init_AddAttribute(new AttributeDateTime("expire", array("allowed_values"=>null, "sql"=>'expire', "default_value"=>'', "is_null_allowed"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeString("temp_id", array("allowed_values"=>null, "sql"=>'temp_id', "default_value"=>'', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeString("item_class", array("allowed_values"=>null, "sql"=>'item_class', "default_value"=>'', "is_null_allowed"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeObjectKey("item_id", array("class_attcode"=>'item_class', "allowed_values"=>null, "sql"=>'item_id', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeInteger("item_org_id", array("allowed_values"=>null, "sql"=>'item_org_id', "default_value"=>'0', "is_null_allowed"=>true, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeBlob("contents", array("is_null_allowed"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));
		MetaModel::Init_AddAttribute(new AttributeString("secret", array("allowed_values"=>null, "sql" => "secret", "default_value"=>'', "is_null_allowed"=>false, "depends_on"=>array(), "always_load_in_tables"=>false)));


		MetaModel::Init_SetZListItems('details', array('temp_id', 'item_class', 'item_id', 'item_org_id'));
		MetaModel::Init_SetZListItems('standard_search', array('temp_id', 'item_class', 'item_id'));
		MetaModel::Init_SetZListItems('list', array('temp_id', 'item_class', 'item_id' ));
	}


	/**
	 * Maps the given context parameter name to the appropriate filter/search code for this class
	 * @param string $sContextParam Name of the context parameter, e.g. 'org_id'
	 * @return string Filter code, e.g. 'customer_id'
	 */
	public static function MapContextParam($sContextParam)
	{
		if ($sContextParam == 'org_id')
		{
			return 'item_org_id';
		}
		else
		{
			return null;
		}
	}

	/**
	 * Set/Update all of the '_item' fields
	 * @param DBObject $oItem Container item
	 * @return void
	 */
	public function SetItem(DBObject $oItem, $bUpdateOnChange = false)
	{
		$sClass = get_class($oItem);
		$iItemId = $oItem->GetKey();

 		$this->Set('item_class', $sClass);
 		$this->Set('item_id', $iItemId);

		$aCallSpec = array($sClass, 'MapContextParam');
		if (is_callable($aCallSpec))
		{
			$sAttCode = call_user_func($aCallSpec, 'org_id'); // Returns null when there is no mapping for this parameter					
			if (MetaModel::IsValidAttCode($sClass, $sAttCode))
			{
				$iOrgId = $oItem->Get($sAttCode);
				if ($iOrgId > 0)
				{
					if ($iOrgId != $this->Get('item_org_id'))
					{
						$this->Set('item_org_id', $iOrgId);
						if ($bUpdateOnChange)
						{
							$this->DBUpdate();
						}
					}
				}
			}
		}
	}

	/**
	 * Give a default value for item_org_id (if relevant...)
	 * @return void
	 */
	public function SetDefaultOrgId()
	{
		// First check that the organization CAN be fetched from the target class
		//
		$sClass = $this->Get('item_class');
		$aCallSpec = array($sClass, 'MapContextParam');
		if (is_callable($aCallSpec))
		{
			$sAttCode = call_user_func($aCallSpec, 'org_id'); // Returns null when there is no mapping for this parameter					
			if (MetaModel::IsValidAttCode($sClass, $sAttCode))
			{
				// Second: check that the organization CAN be fetched from the current user
				//
				if (MetaModel::IsValidClass('Person'))
				{
					$aCallSpec = array($sClass, 'MapContextParam');
					if (is_callable($aCallSpec))
					{
						$sAttCode = call_user_func($aCallSpec, 'org_id'); // Returns null when there is no mapping for this parameter					
						if (MetaModel::IsValidAttCode($sClass, $sAttCode))
						{
							// OK - try it
							//
							$oCurrentPerson = MetaModel::GetObject('Person', UserRights::GetContactId(), false);
							if ($oCurrentPerson)
							{
						 		$this->Set('item_org_id', $oCurrentPerson->Get($sAttCode));
						 	}
						}
					}
				}
			}
		}
	}
	
	/**
	 * When posting a form, finalize the creation of the inline images
	 * related to the specified object
	 * 
	 * @param DBObject $oObject
	 */
	public static function FinalizeInlineImages(DBObject $oObject)
	{
		$iTransactionId = utils::ReadParam('transaction_id', null);
		if (!is_null($iTransactionId))
		{
			// Attach new (temporary) inline images
			
			$sTempId = utils::GetUploadTempId($iTransactionId);
			// The object is being created from a form, check if there are pending inline images for this object
			$sOQL = 'SELECT InlineImage WHERE temp_id = :temp_id';
			$oSearch = DBObjectSearch::FromOQL($sOQL);
			$oSet = new DBObjectSet($oSearch, array(), array('temp_id' => $sTempId));
			while($oInlineImage = $oSet->Fetch())
			{
				$oInlineImage->SetItem($oObject);
				$oInlineImage->Set('temp_id', '');
				$oInlineImage->DBUpdate();
			}
		}
	}
	
	/**
	 * Cleanup the pending images if the form is not submitted
	 * @param string $sTempId
	 */
	public static function OnFormCancel($sTempId)
	{
		// Delete all "pending" InlineImages for this form
		$sOQL = 'SELECT InlineImage WHERE temp_id = :temp_id';
		$oSearch = DBObjectSearch::FromOQL($sOQL);
		$oSet = new DBObjectSet($oSearch, array(), array('temp_id' => $sTempId));
		while($oInlineImage = $oSet->Fetch())
		{
			$oInlineImage->DBDelete();
		}
	}
	
	/**
	 * Parses the supplied HTML fragment to rebuild the attribute src="" for images
	 * that refer to an InlineImage (detected via the attribute data-img-id="") so that
	 * the URL is consistent with the current URL of the application.
	 * @param string $sHtml The HTML fragment to process
	 * @return string The modified HTML
	 */
	public static function FixUrls($sHtml)
	{
		$aNeedles = array();
		$aReplacements = array();
		// Find img tags with an attribute data-img-id
		if (preg_match_all('/<img ([^>]*)data-img-id="([0-9]+)"([^>]*)>/i', $sHtml, $aMatches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE))
		{
			$sUrl = utils::GetAbsoluteUrlAppRoot().INLINEIMAGE_DOWNLOAD_URL;
			foreach($aMatches as $aImgInfo)
			{
				$sImgTag = $aImgInfo[0][0];
				$sSecret = '';
				if (preg_match('/data-img-secret="([0-9a-f]+)"/', $sImgTag, $aSecretMatches))
				{
					$sSecret = '&s='.$aSecretMatches[1];
				}
				$sAttId = $aImgInfo[2][0];
	
				$sNewImgTag = preg_replace('/src="[^"]+"/', 'src="'.htmlentities($sUrl.$sAttId.$sSecret, ENT_QUOTES, 'UTF-8').'"', $sImgTag); // preserve other attributes, must convert & to &amp; to be idempotent with CKEditor
				$aNeedles[] = $sImgTag;
				$aReplacements[] = $sNewImgTag;
			}
			$sHtml = str_replace($aNeedles, $aReplacements, $sHtml);
		}
		return $sHtml;
	}

	/**
	 * Get the javascript fragment  - to be added to "on document ready" - to adjust (on the fly) the width on Inline Images
	 */
	public static function FixImagesWidth()
	{
		$iMaxWidth = (int)MetaModel::GetConfig()->Get('inline_image_max_display_width', 0);
		$sJS = '';
		if ($iMaxWidth != 0)
		{
			$sJS =
<<<EOF
$('img[data-img-id]').each(function() {
	if ($(this).width() > $iMaxWidth)
	{
		$(this).css({'max-width': '{$iMaxWidth}px', width: '', height: '', 'max-height': ''});
	}
	$(this).addClass('inline-image').attr('href', $(this).attr('src'));
}).magnificPopup({type: 'image', closeOnContentClick: true });
EOF
			;
		}
		
		return $sJS;
	}
	
	/**
	 * Check if an the given mimeType is an image that can be processed by the system
	 * @param string $sMimeType
	 * @return boolean
	 */
	public static function IsImage($sMimeType)
	{
		if (!function_exists('gd_info')) return false; // no image processing capability on this system
	
		$bRet = false;
		$aInfo = gd_info(); // What are the capabilities
		switch($sMimeType)
		{
			case 'image/gif':
				return $aInfo['GIF Read Support'];
				break;
					
			case 'image/jpeg':
				return $aInfo['JPEG Support'];
				break;
					
			case 'image/png':
				return $aInfo['PNG Support'];
				break;
	
		}
		return $bRet;
	}
	
	/**
	 * Resize an image so that it fits the maximum width/height defined in the config file
	 * @param ormDocument $oImage The original image stored as an array (content / mimetype / filename)
	 * @return ormDocument The resampled image (or the original one if it already fit)
	 */
	public static function ResizeImageToFit(ormDocument $oImage, &$aDimensions = null)
	{
		$img = false;
		switch($oImage->GetMimeType())
		{
			case 'image/gif':
			case 'image/jpeg':
			case 'image/png':
				$img = @imagecreatefromstring($oImage->GetData());
				break;
					
			default:
				// Unsupported image type, return the image as-is
				$aDimensions = null;
				return $oImage;
		}
		if ($img === false)
		{
			$aDimensions = null;
			return $oImage;
		}
		else
		{
			// Let's scale the image, preserving the transparency for GIFs and PNGs
			$iWidth = imagesx($img);
			$iHeight = imagesy($img);
			$aDimensions = array('width' => $iWidth, 'height' => $iHeight);
			$iMaxImageSize = (int)MetaModel::GetConfig()->Get('inline_image_max_storage_width', 0);
						
			if (($iMaxImageSize > 0) && ($iWidth <= $iMaxImageSize) && ($iHeight <= $iMaxImageSize))
			{
				// No need to resize
				return $oImage;
			}
				
			$fScale = min($iMaxImageSize / $iWidth, $iMaxImageSize / $iHeight);
	
			$iNewWidth = $iWidth * $fScale;
			$iNewHeight = $iHeight * $fScale;
			
			$aDimensions['width'] = $iNewWidth;
			$aDimensions['height'] = $iNewHeight;
				
			$new = imagecreatetruecolor($iNewWidth, $iNewHeight);
				
			// Preserve transparency
			if(($oImage->GetMimeType() == "image/gif") || ($oImage->GetMimeType() == "image/png"))
			{
				imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
				imagealphablending($new, false);
				imagesavealpha($new, true);
			}
				
			imagecopyresampled($new, $img, 0, 0, 0, 0, $iNewWidth, $iNewHeight, $iWidth, $iHeight);
				
			ob_start();
			switch ($oImage->GetMimeType())
			{
				case 'image/gif':
					imagegif($new); // send image to output buffer
					break;
	
				case 'image/jpeg':
					imagejpeg($new, null, 80); // null = send image to output buffer, 80 = good quality
					break;
						
				case 'image/png':
					imagepng($new, null, 5); // null = send image to output buffer, 5 = medium compression
					break;
			}
			$oNewImage = new ormDocument(ob_get_contents(), $oImage->GetMimeType(), $oImage->GetFileName());
			@ob_end_clean();
				
			imagedestroy($img);
			imagedestroy($new);
	
			return $oNewImage;
		}
	
	}

	/**
	 * Get the (localized) textual representation of the max upload size
	 * @return string
	 */
	public static function GetMaxUpload()
	{
		$iMaxUpload = ini_get('upload_max_filesize');
		if (!$iMaxUpload)
		{
			$sRet = Dict::S('Attachments:UploadNotAllowedOnThisSystem');
		}
		else
		{
			$iMaxUpload = utils::ConvertToBytes($iMaxUpload);
			if ($iMaxUpload > 1024*1024*1024)
			{
				$sRet = Dict::Format('Attachment:Max_Go', sprintf('%0.2f', $iMaxUpload/(1024*1024*1024)));
			}
			else if ($iMaxUpload > 1024*1024)
			{
				$sRet = Dict::Format('Attachment:Max_Mo', sprintf('%0.2f', $iMaxUpload/(1024*1024)));
			}
			else
			{
				$sRet = Dict::Format('Attachment:Max_Ko', sprintf('%0.2f', $iMaxUpload/(1024)));
			}
		}
		return $sRet;
	}
	
	/**
	 * Get the fragment of javascript needed to complete the initialization of
	 * CKEditor when creating/modifying an object
	 *
	 * @param DBObject $oObject The object being edited
	 * @param string $sTempId The concatenation of session_id().'_'.$iTransactionId.
	 * @return string The JS fragment to insert in "on document ready"
	 */
	public static function EnableCKEditorImageUpload(DBObject $oObject, $sTempId)
	{
		$sObjClass = get_class($oObject);
		$iObjKey = $oObject->GetKey();

		$sAbsoluteUrlAppRoot = utils::GetAbsoluteUrlAppRoot();
		$sToggleFullScreen = htmlentities(Dict::S('UI:ToggleFullScreen'), ENT_QUOTES, 'UTF-8');
		$sAppRootUrl = utils::GetAbsoluteUrlAppRoot();

		return
<<<EOF
		// Hook the file upload of all CKEditor instances
		$('.htmlEditor').each(function() {
			var oEditor = $(this).ckeditorGet();
			oEditor.config.extraPlugins = 'font,uploadimage';
			oEditor.config.uploadUrl = '$sAbsoluteUrlAppRoot'+'pages/ajax.render.php';
			oEditor.config.filebrowserBrowseUrl = '$sAbsoluteUrlAppRoot'+'pages/ajax.render.php?operation=cke_browse&temp_id=$sTempId&obj_class=$sObjClass&obj_key=$iObjKey';
			oEditor.on( 'fileUploadResponse', function( evt ) {
				var fileLoader = evt.data.fileLoader;
				var xhr = fileLoader.xhr;
				var data = evt.data;
				try {
			        var response = JSON.parse( xhr.responseText );
		
			        // Error message does not need to mean that upload finished unsuccessfully.
			        // It could mean that ex. file name was changes during upload due to naming collision.
			        if ( response.error && response.error.message ) {
			            data.message = response.error.message;
			        }
		
			        // But !uploaded means error.
			        if ( !response.uploaded ) {
			            evt.cancel();
			        } else {
			            data.fileName = response.fileName;
			           	data.url = response.url;
						
			            // Do not call the default listener.
			            evt.stop();
			        }
			    } catch ( err ) {
			        // Response parsing error.
			        data.message = fileLoader.lang.filetools.responseError;
			        window.console && window.console.log( xhr.responseText );
		
			        evt.cancel();
			    }
			} );
	
			oEditor.on( 'fileUploadRequest', function( evt ) {
				evt.data.fileLoader.uploadUrl += '?operation=cke_img_upload&temp_id=$sTempId&obj_class=$sObjClass';
			}, null, null, 4 ); // Listener with priority 4 will be executed before priority 5.
		
			oEditor.on( 'instanceReady', function() {
				if(!CKEDITOR.env.iOS && $('#'+oEditor.id+'_toolbox .editor_magnifier').length == 0)
				{
					$('#'+oEditor.id+'_toolbox').append('<span class="editor_magnifier" title="$sToggleFullScreen" style="display:block;width:12px;height:11px;border:1px #A6A6A6 solid;cursor:pointer; background-image:url(\\'$sAppRootUrl/images/full-screen.png\\')">&nbsp;</span>');
					$('#'+oEditor.id+'_toolbox .editor_magnifier').on('click', function() {
							oEditor.execCommand('maximize');
							if ($(this).closest('.cke_maximized').length != 0)
							{
								$('#'+oEditor.id+'_toolbar_collapser').trigger('click');
							}
					});
				}
				if (oEditor.widgets.registered.uploadimage)
				{
					oEditor.widgets.registered.uploadimage.onUploaded = function( upload ) {
					var oData = JSON.parse(upload.xhr.responseText);
				    	this.replaceWith( '<img src="' + upload.url + '" ' +
				    		'width="' + oData.width + '" ' +
							'height="' + oData.height + '">' );
				    }
				}
			});
		});
EOF
		;
	}
}


/**
 * Garbage collector for cleaning "old" temporary InlineImages (and Attachments).
 * This background process runs every hour and deletes all temporary InlineImages and Attachments
 * whic are are older than one hour.
 */
class InlineImageGC implements iBackgroundProcess
{
    public function GetPeriodicity()
    {
        return 3600; // Runs every hour
    }

	public function Process($iTimeLimit)
	{
		$sDateLimit = date(AttributeDateTime::GetSQLFormat(), time()); // Every temporary InlineImage/Attachment expired will be deleted

		$iProcessed = 0;
		$sOQL = "SELECT InlineImage WHERE (item_id = 0) AND (expire < '$sDateLimit')";
		while (time() < $iTimeLimit)
		{
			// Next one ?
			$oSet = new CMDBObjectSet(DBObjectSearch::FromOQL($sOQL), array('expire' => true) /* order by*/, array(), null, 1 /* limit count */);
			$oSet->OptimizeColumnLoad(array());
			$oResult = $oSet->Fetch();
			if (is_null($oResult))
			{
				// Nothing to be done
				break;
			}
			$iProcessed++;
			$oResult->DBDelete();
		}
		
		$iProcessed2 = 0;
		if (class_exists('Attachment'))
		{
			$sOQL = "SELECT Attachment WHERE (item_id = 0) AND (expire < '$sDateLimit')";
			while (time() < $iTimeLimit)
			{
				// Next one ?
				$oSet = new CMDBObjectSet(DBObjectSearch::FromOQL($sOQL), array('expire' => true) /* order by*/, array(), null, 1 /* limit count */);
				$oSet->OptimizeColumnLoad(array());
				$oResult = $oSet->Fetch();
				if (is_null($oResult))
				{
					// Nothing to be done
					break;
				}
				$iProcessed2++;
				$oResult->DBDelete();
			}		
		}
		return "Cleaned $iProcessed old temporary InlineImage(s) and $iProcessed2 old temporary Attachment(s).";
	}
}