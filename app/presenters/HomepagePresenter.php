<?php

use Nette\Application\UI\Form;

/**
 * My Application
 *
 * @copyright  Copyright (c) 2009 Jan Kuchař
 */



/**
 * Homepage presenter.
 *
 * @author     Jan Kuchař
 */
class HomepagePresenter extends BasePresenter {

	/**
	 * Returns array of suggestions
	 */
	public function checkConfiguration() {
		
		$www = $this->context->expand("%wwwDir%");
		$publicMFUDir = $www . "/MultipleFileUpload/";
		$jsDir        = $www . "/js/";
		$messages = array();
		
		if(!file_exists($publicMFUDir)) {
			$messages[] = "Directory \"www/MultipleFileUpload\" not found! Copy or link content of \"libs/jkuchar/MultipleFileUpload/public\" folder to \"www/MultipleFileUpload\".";
		};
		
		if(!file_exists($publicMFUDir."MFUFallbackController.js")) {
			$messages[] = "File MFUFallbackController.js is missing! Is should be in www/MultipleFileUpload. MFU can't work without this file.";
		};
		
		if(!file_exists($jsDir)) {
			$messages[] = "WARNING: Directory \"www/js\" not found! (trying to check, if jQuery is properly installed)";
		}
		
		if(!file_exists($jsDir."jquery.js")) {
			$messages[] = "WARNING: File jquery.js not found! MultipleFileUpload does not work corectly without jQuery.";
		}
		
		if(!file_exists($jsDir."jquery.livequery.js")) {
			$messages[] = "WARNING: File jquery.livequery.js not found! MFUFallbackController requires livequery.";
		}
		
		if(!file_exists($jsDir."jquery.nette.js")) {
			$messages[] = "WARNING: File jquery.nette.js not found! This is needed if you want to use AJAX.";
		}
		
		if(!file_exists($jsDir."netteForms.js")) {
			$messages[] = "WARNING: File netteForms.js not found! This is needed if you want to use AJAX.";
		}
		
		return $messages;
	}
	
	public function createComponentForm($name) {
		$form = new Form($this, $name);
		$form->getElementPrototype()->class[] = "ajax";

		$form->addText("textField0","Text field")
			->addRule(Form::FILLED, "This is required text field.");

		$form->addMultipleFileUpload("upload","Attachments");
			//->addRule("MultipleFileUpload::validateFilled","You have to upload at least one file!")
			//->addRule("MultipleFileUpload::validateFileSize","Files together are too large.",100*1024);
		//$form->addMultipleFileUpload("upload2","Second file uploader");

		$form->addSubmit("send", "Submit yor form!");
		$form->onSuccess[] = $this->handleFormSuccess;
		
		// Invalidace snippetů
		$form->onError[] = array($this,"handleRedrawForm");
		$form->onSuccess[] = array($this,"handleRedrawForm");
	}

	public function handleFormSuccess(Form $form) {
		$data = $form->getValues();

		// Let's pass our data to template
		$this->template->values = $data;

		$queueId = uniqid();

		// Moving uploaded files
		foreach($data["upload"] AS $file) {
			// $file je instance HttpUploadedFile
			$newFilePath = \Nette\Environment::expand("%appDir%")."/../uploadedFilesDemo/q{".$queueId."}__f{".rand(10,99)."}__".$file->getName();

			// V produkčním módu nepřesunujeme soubory...
			if(!\Nette\Environment::isProduction()) {
				if($file->move($newFilePath))
					$this->flashMessage("File ".$file->getName() . " was successfully moved!");
				else
					$this->flashMessage("Error while moving file ".$file->getName() . ".");
			}
		}
	}

	public function handleRedrawForm() {
		// This invalidates snippet
		// on AJAX requests this causes redrawing of the form
		
		$this->invalidateControl("form");
	}

}
