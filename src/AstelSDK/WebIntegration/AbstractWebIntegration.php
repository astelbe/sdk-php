<?php

namespace AstelSDK\WebIntegration;

use AstelSDK\AstelContext;
use AstelSDK\EmulatedSession;
use AstelSDK\Utils\Singleton;

abstract class AbstractWebIntegration extends Singleton {
	
	public function __construct() {
		$this->context = AstelContext::getInstance();
	}

	public function txtToDisplayNoCookieTechnicalIssue(){
		$noTraceTxt = [
			'EN' => 'Due to a technical constraint, to display the content of this page, you must accept cookies.',
			'FR' => 'Du à une contrainte technique, pour afficher le contenu de cette page, vous devez accepter les cookies.',
			'NL' => 'Als gevolg van een technische beperking moet u, om de inhoud van deze pagina weer te geven, cookies accepteren.',
			'DE' => 'Aufgrund einer technischen Beschränkung müssen Sie Cookies akzeptieren, um den Inhalt dieser Seite anzuzeigen.'
		];
		$toDisplay = '';
		if (!EmulatedSession::isNavigatorAcceptingCookies()) {
			$toDisplay = '<h2 style="color:red;">' . $noTraceTxt[$this->context->getLanguage()] . '</h2>';
		}
		return $toDisplay;
	}

}