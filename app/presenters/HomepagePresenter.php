<?php

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
		$www = $this->context->params["wwwDir"];
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
		$form = new Nette\Application\UI\Form($this, $name);
		$form->getElementPrototype()->class[] = "ajax";

		/*$form->addText("test","Textové políčko")
			->addRule(Form::FILLED, "Textové políčko test musí být vyplněno!");*/

		// Uploadů můžete do formuláře samozdřejmě přidat více, ale zatím je docela nepříjemná validace a jedna chybka v JS
		$form->addMultipleFileUpload("upload","Attachments");
			//->addRule("MultipleFileUpload::validateFilled","Musíte odeslat alespoň jeden soubor!")
			//->addRule("MultipleFileUpload::validateFileSize","Soubory jsou dohromady moc veliké!",100*1024);
		//$form->addMultipleFileUpload("upload2","Druhý balíček souborů");

		$form->addSubmit("odeslat", "Odeslat");
		$form->onSuccess[] = array($this,"zpracujFormular");
		
		// Invalidace snippetů
		$form->onError[] = array($this,"handleRedrawForm");
		$form->onSuccess[] = array($this,"handleRedrawForm");
	}

	public function zpracujFormular(\Nette\Application\UI\Form $form) {
		$data = $form->getValues();

		// Předáme data do šablony
		$this->template->values = $data;

		$queueId = uniqid();

		// Přesumene uploadované soubory
		foreach($data["upload"] AS $file) {
			// $file je instance HttpUploadedFile
			$newFilePath = \Nette\Environment::expand("%appDir%")."/../uploadedFilesDemo/q{".$queueId."}__f{".rand(10,99)."}__".$file->getName();

			// V produkčním módu nepřesunujeme soubory...
			if(!\Nette\Environment::isProduction()) {
				if($file->move($newFilePath))
					$this->flashMessage("Soubor ".$file->getName() . " byl úspěšně přesunut!");
				else
					$this->flashMessage("Při přesouvání souboru ".$file->getName() . " nastala chyba! Pro více informací se podívejte do logů.");
			}
		}
	}

	public function handleRedrawForm() {
		$this->invalidateControl("form");
	}

}
