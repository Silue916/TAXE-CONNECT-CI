<?php

namespace Epaphrodites\epaphrodites\define\lang\fr;

class SetFrenchTextMessages
{
    private $AllMessages;

    public function SwithAnswers($MessageCode)
    {

        $this->AllMessages[] =
            [
                'language' => 'french',
                '403-title' => 'ERREUR 403',
                '404-title' => 'ERREUR 404',
                '419-title' => 'ERREUR 419',
                'session_name' => _SESSION_,
                'token_name' => _CRSF_TOKEN_,
                '500-title' => 'ERREUR 500',
                '403' => 'Acces restreint !!!',
                '500' => 'Internal server error',
                'back' => "Retour page d'accueil",
                'author' => 'Equipe TaxeConnect CI',
                'denie' => "Traitement impossible !!!",
                '419' => 'Votre session a expirée !!!',
                'mdpnotsame' => 'Mot de passe incorrect',
                '404' => 'Oops! Aucune page trouvée !!!',
                'site-title' => 'ACCUEIL | TAXECONNECT CI',
                'description' => 'Plateforme numerique de perception des taxes communales',
                'error_text' => 'Erreur TaxeConnect CI',
                'version' => 'TAXECONNECT CI V1.0',
                'fileempty' => 'Aucun fichier selectionné !!!',
                'false-mail' => 'Désolé, ce mail est incorrect.',
                'noformat' => 'Le format du fichier incorrecte !',
                'succes' => 'Traitement effectué avec succès !!!',
                'login-wrong' => 'Login ou mot de passe incorrecte',
                'user-exist' => 'Désolé, ce utilisateur existe déjà.',
                'connexion' => 'Veuillez vous reconnecter à nouveau svp !',
                'vide' => 'Veuillez remplir correctement tous champs svp !!!',
                'error' => 'Désolé une erreur s\'est produit lors du traitement',
                'rightexist' => 'Désolé ce droit existe déjà pour ce utilisateur',
                'no-identic' => 'Désolé, les mots de passes ne sont pas identiques',
                'erreur' => "Désolé un problème est survenu lors du traitement !!!",
                'send' => 'Félicitation votre message a été envoyé avec succès !!!',
                'no-data' => 'Désolé aucune information ne correspond à votre demande',
                'file-header' => 'Vérifiez l\'en-tête de votre fichier, s\'il vous plaît',
                'done' => 'Félicitation votre inscription a été effectué avec succès !!!',
                'tailleauto' => "La taille du fichier dépasse la limite autorisée de 500 Ko",
                'errorsending' => "Désolé un problème est survenu lors de l'envoi de votre message !!!",
                'denie_action' => "Traitement impossible !!! Vous n'avez pas l'autorisation d'effectué cette action.",
                'keywords' => "TaxeConnect CI, taxes communales, paiement mobile money, preuve QR, Cote dIvoire;",
            ];

        return isset($this->AllMessages[0][$MessageCode]) ? $this->AllMessages[0][$MessageCode] : "";
    }
}


