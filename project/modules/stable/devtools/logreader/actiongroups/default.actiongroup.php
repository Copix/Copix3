<?php
/**
 * @package devtools
 * @subpackage logreader
 * @author Steevan BARBOYON
 * @copyright CopixTeam
 * @link http://copix.org
 * @license http://www.gnu.org/licenses/lgpl.html GNU General Lesser Public Licence, see LICENCE file
 */

/**
 * Lecture d'un fichier de log type Linux
 *
 * @package devtools
 * @subpackage logreader
 */
class ActionGroupDefault extends CopixActionGroup {
	/**
	 * Vérifie que l'on est bien administrateur
	 *
	 * @param string $pActionName Nom de l'action
	 */
	protected function _beforeAction ($pActionName) {
		if (strtolower ($pActionName) != 'lognewlines') {
			CopixAuth::getCurrentUser ()->assertCredential ('basic:admin');
		}
	}

	/**
	 * Liste des fichiers de log disponibles
	 *
	 * @return CopixActionReturn
	 */
	public function processDefault () {
		$ppo = LogReaderTools::setPage (LogReaderTools::PAGE_LIST);
		$ppo->files = LogReaderServices::getList ();
		$ppo->highlight = _request ('highlight');
		return _arPPO ($ppo, 'list.php');
	}

	/**
	 * Edition d'un fichier de log
	 *
	 * @return CopixActionReturn
	 */
	public function processEdit () {
		$mode = (_request ('file') != null) ? 'edit' : 'add';
		$errors = _request ('errors');

		if ($mode == 'add') {
			$ppo = LogReaderTools::setPage (LogReaderTools::PAGE_ADD);
			$ppo->file = ($errors == null) ? LogReaderServices::create () : CopixSession::get ('file_edit_' . $errors, 'logreader');
		} else {
			$ppo = LogReaderTools::setPage (LogReaderTools::PAGE_EDIT);
			$ppo->file = ($errors == null) ? LogReaderServices::get (_request ('file')) : CopixSession::get ('file_edit_' . $errors, 'logreader');
		}
		$ppo->mode = $mode;
		$ppo->types = LogReaderServices::getTypes ();

		if (_request ('errors') != null) {
			$ppo->errors = CopixSession::get ('file_edit_' . _request ('errors'), 'logreader')->isValid ()->asArray ();
		}

		return _arPPO ($ppo, 'edit.php');
	}

	/**
	 * Effectue l'édition
	 *
	 * @return CopixActionReturn
	 */
	public function processDoEdit () {
		$mode = (_request ('file') != null) ? 'edit' : 'add';

		if ($mode == 'add') {
			LogReaderTools::setPage (LogReaderTools::PAGE_DO_ADD);
			$file = LogReaderServices::create ();
		} else {
			LogReaderTools::setPage (LogReaderTools::PAGE_DO_EDIT);
			$file = LogReaderServices::get (_request ('file'));
		}

		$file->setFilePath (_request ('path'));
		$file->setRotationFilePath (_request ('rotation'));
		$file->setType (_request ('type'));

		if ($file->isValid () !== true) {
			$id = uniqid ();
			CopixSession::set ('file_edit_' . $id, $file, 'logreader');
			return _arRedirect (_url ('logreader||edit', array ('file' => $file->getId (), 'errors' => $id)));
		}

		if ($mode == 'add') {
			$file = LogReaderServices::add ($file);
		} else {
			LogReaderServices::update ($file);
		}
		
		$params = array (
			'title' => 'Confirmation',
			'redirect_url' => _url ('logreader||', array ('highlight' => $file->getId ())),
			'message' => 'Le fichier de log ' . $file->getFileName () . ' a été sauvegardé.',
			'links' => array (
				_url ('logreader||', array ('highlight' => $file->getId ())) => 'Retour à la liste des fichiers de log',
				_url ('logreader||edit', array ('file' => $file->getId ())) => 'Retour à l\'édition du fichier de log'
			)
		);
		return CopixActionGroup::process ('generictools|messages::getInformation', $params);
	}

	/**
	 * Renvoi juste une image indiquant si le fichier existe, à appeler en ajax
	 *
	 * @return CopixActionReturn
	 */
	public function processFileExists () {
		if (file_exists (_request ('path'))) {
			$src = 'success';
			$title = 'Fichier de log trouvé';
		} else {
			$src = 'error';
			$title = 'Fichier de log non trouvé';
		}
		return _arString ('<img src="' . _resource ('logreader|img/' . $src . '.png') . '" alt="' . $title . '" title="' . $title . '" />');
	}

	/**
	 * Confirmation de suppression de log
	 *
	 * @return CopixActionReturn
	 */
	public function processDelete () {
		$ppo = LogReaderTools::setPage (LogReaderTools::PAGE_DELETE);
		$file = LogReaderServices::get (_request ('file'));

		return CopixActionGroup::process (
			'generictools|Messages::getConfirm',
			array (
				'message' => 'Etes-vous sur de vouloir supprimer la configuration du fichier de log "' . $file->getFileName () . '" ?',
				'confirm' => _url ('logreader||doDelete', array ('file' => $file->getId ())),
				'cancel' => _url ('logreader||'),
				'title' => 'Confirmation de suppression'
			)
		);
	}

	/**
	 * Effectue la suppression
	 *
	 * @return CopixActionReturn
	 */
	public function processDoDelete () {
		$ppo = LogReaderTools::setPage (LogReaderTools::PAGE_DO_DELETE);
		$file = LogReaderServices::get (_request ('file'));
		LogReaderServices::delete ($file->getId ());

		return CopixActionGroup::process (
			'generictools|Messages::getInformation',
			array (
				'title' => 'Suppression effectuée',
				'message' => 'La suppression de la configuration du log "' . $file->getFileName () . '" a été effectuée.',
				'links' => array ('logreader||' => 'Retour à la liste des logs'),
				'redirect_url' => 'logreader||'
			)
		);
	}

	/**
	 * Affiche le contenu du log
	 *
	 * @return CopixActionReturn
	 */
	public function processShow () {
		$ppo = LogReaderTools::setPage (LogReaderTools::PAGE_SHOW);
		$ppo->rotation = _request ('rotation');
		$ppo->file = LogReaderServices::get (_request ('file'));
		$file = ($ppo->rotation == null) ? $ppo->file : LogReaderServices::getRotation (_request ('file'), $ppo->rotation);
		if (($since = CopixSession::get ('lastline_' . $file->getFilePath (), 'logreader')) != null) {
			$ppo->lastLines = $file->getLines ($since + 1, 1000);
		}
		$ppo->linesCount = $file->linesCount ();
		$ppo->linesPerPage = 20;
		$first = _request ('first', max (1, $ppo->linesCount - $ppo->linesPerPage + 1));
		$ppo->lines = $file->getLines ($first, $ppo->linesPerPage);
		$ppo->first = $first;
		$ppo->nextFirst = max ($ppo->first - $ppo->linesPerPage, 1);
		$ppo->rotations = LogReaderServices::getRotations ($file->getId ());

		CopixSession::set ('lastline_' . $file->getFilePath (), $ppo->linesCount, 'logreader');

		return _arPPO ($ppo, 'show.php');
	}

	/**
	 * Effectue un log de type errors sur les nouvelles lignes du log demandé
	 *
	 * @return CopixActionReturn
	 */
	public function processLogNewLines () {
		if (CopixConfig::get ('logreader|logNewLines') == 0) {
			return _arNone ();
		}

		$files = array ();
		if (CopixRequest::exists ('file')) {
			$files[] = LogReaderServices::get (_request ('file'));
		} else {
			$files = LogReaderServices::getList ();
		}

		foreach ($files as $file) {
			// recherche des nouvelles lignes
			$newLines = $file->getNewLines ();
			$firstLine = $file->getLastReadFirstLine ();
			if (count ($newLines) > 0) {
				$lines = $file->getLines (1, 1);
				$firstLine = $lines[0]->getText ();
				foreach ($newLines as $line) {
					$extras = array (
						'line_text' => $line->getText (),
						'line_type' => $line->getType (),
						'line_date' => $line->getDate ()
					);
					_log ($line->getShortText (), 'errors', CopixLog::ERROR, $extras);
				}
			}

			// mise à jour de la dernière lecture
			$file->setLastReadDate (filemtime ($file->getFilePath ()));
			$file->setLastReadLine ($file->linesCount ());
			$file->setLastReadFirstLine ($firstLine);
			LogReaderServices::update ($file);
		}

		return _arNone ();
	}
}