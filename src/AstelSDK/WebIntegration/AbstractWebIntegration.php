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
//		$noTraceTxt = [
//			'EN' => 'Due to a technical constraint, to display the content of this page, you must accept cookies.',
//			'FR' => 'Du à une contrainte technique, pour afficher le contenu de cette page, vous devez accepter les cookies.',
//			'NL' => 'Als gevolg van een technische beperking moet u, om de inhoud van deze pagina weer te geven, cookies accepteren.',
//			'DE' => 'Aufgrund einer technischen Beschränkung müssen Sie Cookies akzeptieren, um den Inhalt dieser Seite anzuzeigen.'
//		];
//		$toDisplay = '';
        // TODO refactor
        // keep execting this method as it set a default cookieconsent_status cookie
        EmulatedSession::isNavigatorAcceptingCookies();
//		if (!EmulatedSession::isNavigatorAcceptingCookies()) {
//			$toDisplay = '<h2 style="color:blue;">' . $noTraceTxt[$this->context->getLanguage()] . '</h2>';
//		}
		return '';
	}
	
	public function getPageURL(){
		return $_SERVER['HTTP_X_FORWARDED_PROTO'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}

}