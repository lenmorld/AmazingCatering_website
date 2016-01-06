<?php

class c1_Upload
{

          protected $_uploaded = array(); 
          protected $_destination; 
          protected $_max = 2097152; 		// if max size is not set in the upload_page, this one's the default
          protected $_messages = array(); 
		  
		  /*/##########################################
		  
			
		  THE PERMITTED ARRAY CONTAINS ONLY COMMON IMAGES MIME_TYPES (GIF,JPEG, JPG, PNG)
		  
		  ADD STUFF TO THIS ONE IF YOU WANT TO UPLOAD OTHER TYPES
		  
		  YOU MUST KNOW THE MIME-TYPES FOR CERTAIN FILE FORMATS
		  
		  YOU CAN FIND MIME-TYPE LIST HERE
		  
		  http://www.webmaster-toolkit.com/mime-types.shtml
		  
		  
		  
		  //############################################# */
		  
          protected $_permitted = array('image/gif', 		
                                        'image/jpeg', 
										'image/jpg',
                                        'image/pjpeg', 
                                        'image/png'); 
          protected $_renamed = false;
		  
		  protected $_filenames = array();
		  
	public function __construct($path) 
		{ 
			if (!is_dir($path) || !is_writable($path)) { 
            throw new Exception("$path must be a valid, writable directory.");
            } 
            $this->_destination = $path; 
            $this->_uploaded = $_FILES; 
          }
		  
        public function move($overwrite = false) { 
          $field = current($this->_uploaded); 
		  
          if (is_array($field['name'])) { 
            foreach ($field['name'] as $number => $filename) 
					{ 
              // process multiple upload 
              $this->_renamed = false; 
              $this->processFile($filename, $field['error'][$number], 
                              $field['size'][$number], $field['type'][$number], 
                              $field['tmp_name'][$number], $overwrite);  
					} 
          } 
		  else 
		    {
		  
          $this->processFile($field['name'], $field['error'], $field['size'],
                            $field['type'], $field['tmp_name'], $overwrite);
			}

        }
		
		protected function processFile($filename, $error, $size, $type, $tmp_name, $overwrite)
		{
		  $OK = $this->checkError($filename, $error); 
		  if ($OK) 
			{
			$typeOK = $this->checkType($filename, $type);
			$sizeOK = $this->checkSize($filename, $size); 
             
				if ($sizeOK && $typeOK) 
				{
				  $name = $this->checkName($filename, $overwrite);
				
				  $success = move_uploaded_file($tmp_name, $this->_destination .
					$name ); 
					
				  if ($success) 
					  { 
						//ch16-----------------------
							//add the amended filename to the array of filenames
							$this->_filenames[] = $name;
						//---------------------------
						$message = $filename . ' uploaded successfully'; 
						
						if ($this->_renamed) { 
						  $message .= " and renamed $name"; 
						} 
						$this->_messages[] = $message;
					  } 
				   else { 
						$this->_messages[] = 'Could not upload ' . $filename; 
					  } 
				 }
			}	
		}
		
		
		//ch16---------------------
			public function getFilenames()
				{
					return $this->_filenames;
				}
		//-------------------------

		
        public function getMaxSize() { 
          return number_format($this->_max/1024, 1) . 'kB'; 
        }
		

        protected function checkError($filename, $error) { 
          switch ($error) { 
            case 0: 
              return true; 
            case 1: 
            case 2: 
              $this->_messages[] = "$filename exceeds maximum size: " . 
                $this->getMaxSize(); 
              return true; 
			  //return false;
            case 3: 
			
              $this->_messages[] = "Error uploading $filename. Please try again."; 
              return false; 
            case 4: 
              $this->_messages[] = 'No file selected.'; 
              return false; 
            default: 
              $this->_messages[] = "System error uploading $filename. Contact 
                webmaster."; 
              return false; 
          } 
        }
		
       protected function checkSize($filename, $size) { 
          if ($size == 0) { 
            return false; 
          } elseif ($size > $this->_max) { 
            $this->_messages[] = "$filename exceeds maximum size: " .
              $this->getMaxSize(); 
            return false; 
          } else { 
            return true; 
          } 
        } 
		
        protected function checkType($filename, $type) { 
		
			if (empty($type))
			{
			 return false;
			 }
          elseif (!in_array($type, $this->_permitted)) { 
            $this->_messages[] = "$filename is not a permitted type of file."; 
            return false; 
          } else { 
            return true; 
          } 
        }
		
        public function getMessages() { 
          return $this->_messages; 
        }
		
        public function addPermittedTypes($types) { 
          $types = (array) $types; 
          $this->isValidMime($types); 
          $this->_permitted = array_merge($this->_permitted, $types); 
        }
		
        public function setPermittedTypes($types) { 
          $types = (array) $types; 
          $this->isValidMime($types); 
          $this->_permitted = $types; 
		  
        } 
		
		
		/*#############################################################
			
		THIS LISTS THE VALID TYPES (NOT TO BE CONFUSED WITH PERMITTED TYPES ABOVE)
		
		[A TYPE CAN BE VALID, BUT NOT PERMITTED]
		
		SO I PUT ALL POSSIBLE TYPES HERE
		
		*/#############################################################
		
        protected function isValidMime($types) { 
          $alsoValid = array(
							//images
									'image/gif', 
									'image/jpeg', 
									'image/jpg',
									'image/pjpeg', 
									'image/png',
									'image/tiff',
							  //pdf,msword, text file, rtf
									'application/pdf', 
									 'text/plain', 
									 'text/rtf',
									 'application/msword',
							 //zip files
									'application/zip', 						
									'application/x-zip', 
									'application/x-zip-compressed', 
									'application/octet-stream', 
									'application/x-compress', 
									'application/x-compressed', 
									'multipart/x-zip',
							  //rar
							        'application/x-rar-compressed',
							  //text file
									'text/plain',
							  //rtf
									'text/rtf',
									'text/richtext',
							//ms office			
									'application/msword',
									'application/vnd.ms-powerpoint',
									'application/vnd.ms-excel',
									'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
									'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
									'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
									'application/vnd.openxmlformats-officedocument.presentationml.template',
									'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
									'application/vnd.openxmlformats-officedocument.presentationml.presentation',
									'application/vnd.openxmlformats-officedocument.presentationml.slide',
									'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
									'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
									'application/vnd.ms-excel.addin.macroEnabled.12',
									'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
					        //pdf
							        'application/pdf',
									'application/x-pdf',
							//avi
									'video/avi', 
									'video/msvideo', 
									'video/x-msvideo', 
									'image/avi', 
									'video/xmpg2', 
									'application/x-troff-msvideo', 
									'audio/aiff', 
									'audio/avi',		
							//ogv-HTML5
												'application/ogg',
												'video/ogv',
												'video/ogg',
												'audio/ogg',
												'audio/flac',
												'application/annodex',
												'audio/annodex',
												'video/annodex',
												'application/xspf+xml',
												'audio/vorbis',
												'video/theora',
												'audio/speex',
												'audio/flac',
												'text/cmml',
												'application/kate',											
							//mp3
								 	'audio/mpeg3',
								 	'audio/x-mpeg-3',
								 	'video/mpeg',
								 	'video/x-mpeg'
							);  

          $valid = array_merge($this->_permitted, $alsoValid); 
          foreach ($types as $type) { 
            if (!in_array($type, $valid)) { 
              throw new Exception("$type is not a permitted MIME type"); 
            } 
          } 
        }
		
        public function setMaxSize($num) { 
          if (!is_numeric($num)) { 
            throw new Exception("Maximum size must be a number."); 
          } 
          $this->_max = (int) $num; 
        } 
		
        protected function checkName($name, $overwrite) { 
          $nospaces = str_replace(' ', '_', $name); 
          if ($nospaces != $name) { 
            $this->_renamed = true; 
          } 
          if (!$overwrite) { 
            // rename the file if it already exists
            $existing = scandir($this->_destination); 
            if (in_array($nospaces, $existing)) { 
              $dot = strrpos($nospaces, '.'); 
              if ($dot) { 
                $base = substr($nospaces, 0, $dot); 
                $extension = substr($nospaces, $dot); 
              } else { 
                $base = $nospaces; 
                $extension = ''; 
              } 
              $i = 1; 
              do { 
                $nospaces = $base . '_' . $i++ . $extension; 
              } while (in_array($nospaces, $existing)); 
              $this->_renamed = true; 
            }			  
          } 
          return $nospaces; 
        } 
		
}