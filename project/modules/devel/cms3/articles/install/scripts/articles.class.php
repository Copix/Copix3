<?php
/**
 * Mises à jour du module articles
 */
class CopixModuleInstallerArticles extends CopixAbstractModuleInstaller {
	/**
	 * Version 1.0.0 à 1.1.0
	 */
	public function process1_0_0_to_1_1_0 () {
		$daoHeading = DAOheadingelementinformation::instance ();
		foreach (DAOarticles::instance ()->findAll () as $article) {
			$records = $daoHeading->findBy (_daoSP ()->addCondition ('id_helt', '=', $article->id_article)->addcondition ('type_hei', '=', 'article'));
			if (count ($records) == 1) {
				$record = $records[0];
				$record->description_hei = $article->description_article;
				$daoHeading->update ($record);
			} else {
				_log ('L\'article "' . $article->id_article . '" n\'a pas d\'enregistrement dans headingelementinformation.', 'errors');
			}
		}
		_doQuery ('alter table articles drop column description_article');
	}

	public function process1_1_0_to_1_2_0 () {
		_doQuery ('RENAME TABLE articles TO cms_articles');
		_doQuery ('ALTER TABLE cms_articles CHANGE summary summary_article TEXT NULL');
		_doQuery ('ALTER TABLE cms_articles CHANGE content content_article TEXT NULL');
	}
}